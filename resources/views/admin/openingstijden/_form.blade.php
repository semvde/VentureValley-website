@php
    /** @var \App\Models\OpeningHourException|null $exception */
    $exception ??= null;
@endphp

@csrf

<div class="flex gap-3 max-sm:flex-col">
    <div class="flex flex-col flex-1">
        <label for="date">{{ $exception ? 'Datum' : 'Datum (vanaf)' }}</label>
        <input type="date" name="date" id="date" required class="input"
               value="{{ old('date', $exception?->date->toDateString()) }}">
        @error('date')
        <span class="text-red-700">{{ $message }}</span>
        @enderror
    </div>
    @unless($exception)
        <div class="flex flex-col flex-1">
            <label for="end_date">T/m (optioneel)</label>
            <input type="date" name="end_date" id="end_date" class="input" value="{{ old('end_date') }}">
            <p class="text-gray-400 text-sm italic">Vul dit in om dezelfde tijden voor een hele periode in te
                stellen.</p>
            @error('end_date')
            <span class="text-red-700">{{ $message }}</span>
            @enderror
        </div>
    @endunless
</div>

<div class="flex flex-row gap-4 items-center">
    <input type="hidden" name="is_closed" value="0">
    <input type="checkbox" name="is_closed" id="is_closed" value="1" class="input"
        @checked(old('is_closed', $exception?->is_closed))>
    <label for="is_closed">De hele dag gesloten</label>
</div>

<div class="flex gap-3 max-sm:flex-col" id="time-fields">
    <div class="flex flex-col flex-1">
        <label for="open_time">Open vanaf</label>
        <input type="time" name="open_time" id="open_time" class="input"
               value="{{ old('open_time', $exception?->open_time ? substr($exception->open_time, 0, 5) : config('park.opening_hours.open')) }}">
        @error('open_time')
        <span class="text-red-700">{{ $message }}</span>
        @enderror
    </div>
    <div class="flex flex-col flex-1">
        <label for="close_time">Open tot</label>
        <input type="time" name="close_time" id="close_time" class="input"
               value="{{ old('close_time', $exception?->close_time ? substr($exception->close_time, 0, 5) : config('park.opening_hours.close')) }}">
        @error('close_time')
        <span class="text-red-700">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="flex flex-col">
    <label for="note">Notitie (optioneel)</label>
    <input type="text" name="note" id="note" maxlength="100" class="input"
           value="{{ old('note', $exception?->note) }}"
           placeholder="Bijvoorbeeld: Halloween-evenement. Dit is zichtbaar in de kalender">
    @error('note')
    <span class="text-red-700">{{ $message }}</span>
    @enderror
</div>

<script>
    (() => {
        const closed = document.getElementById('is_closed');
        const fields = document.getElementById('time-fields');
        const toggle = () => {
            fields.classList.toggle('hidden', closed.checked);
            fields.querySelectorAll('input').forEach(input => input.required = !closed.checked);
        };
        closed.addEventListener('change', toggle);
        toggle();
    })();
</script>
