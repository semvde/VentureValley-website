<?php

namespace App\Http\Controllers;

use App\Models\OpeningHourException;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpeningHourExceptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showPast = $request->boolean('verleden');

        $exceptions = OpeningHourException::query()
            ->when(! $showPast, fn ($query) => $query->upcoming())
            ->orderBy('date')
            ->get();

        return view('admin.openingstijden.index', compact('exceptions', 'showPast'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.openingstijden.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'end_date' => 'nullable|date|after:date',
            ...$this->hoursRules(),
        ]);

        $start = CarbonImmutable::parse($validated['date']);
        $end = isset($validated['end_date']) ? CarbonImmutable::parse($validated['end_date']) : $start;

        if ($start->diffInDays($end) > 365) {
            throw ValidationException::withMessages([
                'end_date' => 'Een periode mag maximaal een jaar lang zijn.',
            ]);
        }

        $dates = collect(CarbonPeriod::create($start, $end))->map->toDateString();

        $existing = OpeningHourException::whereIn('date', $dates)->orderBy('date')->get();
        if ($existing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'date' => 'Er bestaat al een uitzondering op: '
                    .$existing->map(fn ($exception) => $exception->date->format('d-m-Y'))->join(', ')
                    .'. Bewerk of verwijder die eerst.',
            ]);
        }

        $attributes = $this->hoursAttributes($validated);

        DB::transaction(function () use ($dates, $attributes) {
            foreach ($dates as $date) {
                OpeningHourException::create(['date' => $date, ...$attributes]);
            }
        });

        $message = $dates->count() === 1
            ? "De uitzondering op {$start->format('d-m-Y')} is aangemaakt!"
            : "De uitzondering van {$start->format('d-m-Y')} t/m {$end->format('d-m-Y')} is aangemaakt!";

        return redirect()->route('admin.openingstijden.index')
            ->with('alert', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpeningHourException $exception)
    {
        return view('admin.openingstijden.edit', compact('exception'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OpeningHourException $exception)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:opening_hour_exceptions,date,'.$exception->id,
            ...$this->hoursRules(),
        ]);

        $exception->update(['date' => $validated['date'], ...$this->hoursAttributes($validated)]);

        return redirect()->route('admin.openingstijden.index')
            ->with('alert', "De uitzondering op {$exception->date->format('d-m-Y')} is bijgewerkt!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpeningHourException $exception)
    {
        $exception->delete();

        return redirect()->route('admin.openingstijden.index')
            ->with('alert', "De uitzondering op {$exception->date->format('d-m-Y')} is verwijderd!");
    }

    private function hoursRules(): array
    {
        return [
            'is_closed' => 'boolean',
            'open_hour' => 'exclude_if:is_closed,1|required|integer|between:0,23',
            'close_hour' => 'exclude_if:is_closed,1|required|integer|between:1,24|gt:open_hour',
            'note' => 'nullable|string|max:100',
        ];
    }

    private function hoursAttributes(array $validated): array
    {
        $isClosed = (bool) ($validated['is_closed'] ?? false);

        return [
            'is_closed' => $isClosed,
            'open_hour' => $isClosed ? null : $validated['open_hour'],
            'close_hour' => $isClosed ? null : $validated['close_hour'],
            'note' => $validated['note'] ?? null,
        ];
    }
}
