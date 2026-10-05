<?php

use App\Models\OpeningHourException;
use App\Models\User;
use Illuminate\Support\Carbon;

beforeEach(function () {
    Carbon::setTestNow('2026-10-05 12:00:00');
});

function admin(): User
{
    $user = User::factory()->create();
    $user->forceFill(['role' => 'admin'])->save();

    return $user;
}

test('calendar shows default opening hours for the current month', function () {
    $this->get('/openingstijden')
        ->assertOk()
        ->assertSee('Oktober 2026')
        ->assertSee('07:00 – 23:59');
});

test('calendar shows exceptions', function () {
    OpeningHourException::create(['date' => '2026-10-10', 'open_time' => '07:00', 'close_time' => '20:00']);
    OpeningHourException::create(['date' => '2026-10-11', 'is_closed' => true, 'note' => 'Onderhoud']);

    $this->get('/openingstijden')
        ->assertOk()
        ->assertSee('07:00 – 20:00')
        ->assertSee('Gesloten')
        ->assertSee('Onderhoud');
});

test('calendar month is limited to the current month and three months ahead', function () {
    $this->get('/openingstijden?maand=2027-01')->assertOk()->assertSee('Januari 2027');
    $this->get('/openingstijden?maand=2027-05')->assertOk()->assertSee('Januari 2027');
    $this->get('/openingstijden?maand=2025-01')->assertOk()->assertSee('Oktober 2026');
    $this->get('/openingstijden?maand=onzin')->assertOk()->assertSee('Oktober 2026');
});

test('guests cannot manage exceptions', function () {
    $this->get('/admin/openingstijden')->assertNotFound();
    $this->post('/admin/openingstijden', [])->assertNotFound();
    expect(OpeningHourException::count())->toBe(0);
});

test('admin can create a single exception', function () {
    $this->actingAs(admin())
        ->post('/admin/openingstijden', [
            'date' => '2026-10-10',
            'is_closed' => '0',
            'open_time' => '07:00',
            'close_time' => '20:00',
        ])
        ->assertRedirect('/admin/openingstijden')
        ->assertSessionHasNoErrors();

    $exception = OpeningHourException::sole();
    expect($exception->date->toDateString())->toBe('2026-10-10')
        ->and($exception->close_time)->toStartWith('20:00');
});

test('admin can create an exception for a period', function () {
    $this->actingAs(admin())
        ->post('/admin/openingstijden', [
            'date' => '2026-11-01',
            'end_date' => '2026-11-07',
            'is_closed' => '1',
        ])
        ->assertSessionHasNoErrors();

    expect(OpeningHourException::count())->toBe(7)
        ->and(OpeningHourException::where('is_closed', true)->whereNull('open_time')->count())->toBe(7);
});

test('creating a period fails when a day already has an exception', function () {
    OpeningHourException::create(['date' => '2026-11-03', 'is_closed' => true]);

    $this->actingAs(admin())
        ->post('/admin/openingstijden', [
            'date' => '2026-11-01',
            'end_date' => '2026-11-07',
            'is_closed' => '1',
        ])
        ->assertSessionHasErrors('date');

    expect(OpeningHourException::count())->toBe(1);
});

test('closing time must be after opening time', function () {
    $this->actingAs(admin())
        ->post('/admin/openingstijden', [
            'date' => '2026-10-10',
            'open_time' => '20:00',
            'close_time' => '07:00',
        ])
        ->assertSessionHasErrors('close_time');
});

test('admin can update and delete an exception', function () {
    $exception = OpeningHourException::create(['date' => '2026-10-10', 'open_time' => '07:00', 'close_time' => '20:00']);

    $this->actingAs(admin())
        ->put("/admin/openingstijden/{$exception->id}", [
            'date' => '2026-10-10',
            'is_closed' => '1',
        ])
        ->assertSessionHasNoErrors();

    expect($exception->fresh()->is_closed)->toBeTrue()
        ->and($exception->fresh()->open_time)->toBeNull();

    $this->actingAs(admin())->delete("/admin/openingstijden/{$exception->id}");

    expect(OpeningHourException::count())->toBe(0);
});
