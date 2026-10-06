<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('opening_hour_exceptions', function (Blueprint $table) {
            $table->unsignedTinyInteger('open_hour')->nullable()->after('is_closed');
            $table->unsignedTinyInteger('close_hour')->nullable()->after('open_hour');
        });

        // Bestaande tijden omzetten naar hele uren; minuten worden naar boven afgerond (23:59 wordt 24).
        $toHour = function (?string $time): ?int {
            if ($time === null) {
                return null;
            }

            [$hours, $minutes] = array_map('intval', explode(':', $time));

            return min(24, $hours + ($minutes > 0 ? 1 : 0));
        };

        DB::table('opening_hour_exceptions')->orderBy('id')->each(function ($row) use ($toHour) {
            DB::table('opening_hour_exceptions')->where('id', $row->id)->update([
                'open_hour' => $row->is_closed ? null : intval(explode(':', $row->open_time)[0]),
                'close_hour' => $row->is_closed ? null : $toHour($row->close_time),
            ]);
        });

        Schema::table('opening_hour_exceptions', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opening_hour_exceptions', function (Blueprint $table) {
            $table->time('open_time')->nullable()->after('is_closed');
            $table->time('close_time')->nullable()->after('open_time');
        });

        DB::table('opening_hour_exceptions')->orderBy('id')->each(function ($row) {
            DB::table('opening_hour_exceptions')->where('id', $row->id)->update([
                'open_time' => $row->open_hour === null ? null : sprintf('%02d:00', $row->open_hour),
                'close_time' => $row->close_hour === null ? null : ($row->close_hour >= 24 ? '23:59' : sprintf('%02d:00', $row->close_hour)),
            ]);
        });

        Schema::table('opening_hour_exceptions', function (Blueprint $table) {
            $table->dropColumn(['open_hour', 'close_hour']);
        });
    }
};
