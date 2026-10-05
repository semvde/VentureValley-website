<?php

namespace App\Http\Controllers;

use App\Services\OpeningHours;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show()
    {
        return view('home');
    }

    public function bezoeken()
    {
        return view('bezoeken');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function parkreglement()
    {
        return view('parkreglement');
    }

    public function openingstijden(Request $request, OpeningHours $openingHours)
    {
        $firstMonth = CarbonImmutable::today()->startOfMonth();
        $lastMonth = $firstMonth->addMonths(config('park.calendar_months_ahead'));

        $month = $firstMonth;
        if (preg_match('/^\d{4}-\d{2}$/', (string) $request->query('maand'))) {
            $requested = CarbonImmutable::createFromFormat('!Y-m', $request->query('maand'));
            $month = $requested->max($firstMonth)->min($lastMonth);
        }

        $weeks = $openingHours->forMonth($month->year, $month->month);
        $previousMonth = $month->gt($firstMonth) ? $month->subMonth() : null;
        $nextMonth = $month->lt($lastMonth) ? $month->addMonth() : null;

        return view('openingstijden', compact('month', 'weeks', 'previousMonth', 'nextMonth'));
    }
}
