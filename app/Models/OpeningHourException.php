<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OpeningHourException extends Model
{
    protected $fillable = ['date', 'is_closed', 'open_time', 'close_time', 'note'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_closed' => 'boolean',
        ];
    }

    /**
     * Sla de datum altijd op als Y-m-d, zodat vergelijkingen en de unique-regel kloppen.
     */
    protected function date(): Attribute
    {
        return Attribute::set(fn ($value) => Carbon::parse($value)->toDateString());
    }

    public function scopeForMonth(Builder $query, int $year, int $month): void
    {
        $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->whereDate('date', '>=', today());
    }
}
