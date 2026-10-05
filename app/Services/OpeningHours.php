<?php

namespace App\Services;

use App\Models\OpeningHourException;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class OpeningHours
{
    /**
     * Geeft de openingstijden voor één dag terug.
     */
    public function forDate(CarbonInterface $date): array
    {
        $exception = OpeningHourException::whereDate('date', $date)->first();

        return $this->resolve(CarbonImmutable::parse($date)->startOfDay(), $exception);
    }

    /**
     * Geeft de kalenderweken voor een maand terug (maandag t/m zondag).
     * Vakjes buiten de maand zijn null.
     */
    public function forMonth(int $year, int $month): Collection
    {
        $start = CarbonImmutable::create($year, $month, 1);

        $exceptions = OpeningHourException::forMonth($year, $month)
            ->get()
            ->keyBy(fn (OpeningHourException $exception) => $exception->date->toDateString());

        $days = collect(array_fill(0, $start->dayOfWeekIso - 1, null));

        for ($date = $start; $date->month === $month; $date = $date->addDay()) {
            $days->push($this->resolve($date, $exceptions->get($date->toDateString())));
        }

        $days = $days->pad((int) ceil($days->count() / 7) * 7, null);

        return $days->chunk(7)->map->values();
    }

    private function resolve(CarbonImmutable $date, ?OpeningHourException $exception): array
    {
        if ($exception === null) {
            return [
                'date' => $date,
                'is_closed' => false,
                'open' => config('park.opening_hours.open'),
                'close' => config('park.opening_hours.close'),
                'note' => null,
                'is_exception' => false,
            ];
        }

        return [
            'date' => $date,
            'is_closed' => $exception->is_closed,
            'open' => $exception->is_closed ? null : substr($exception->open_time, 0, 5),
            'close' => $exception->is_closed ? null : substr($exception->close_time, 0, 5),
            'note' => $exception->note,
            'is_exception' => true,
        ];
    }
}
