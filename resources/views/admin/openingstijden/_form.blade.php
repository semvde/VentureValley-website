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
        <label for="open_hour">Open vanaf</label>
        <select name="open_hour" id="open_hour" class="input">
            @foreach(range(0, 23) as $hour)
                <option value="{{ $hour }}" @selected((int) old('open_hour', $exception?->open_hour ?? config('park.opening_hours.open')) === $hour)>
                    {{ $hour }} uur
                </option>
            @endforeach
        </select>
        @error('open_hour')
        <span class="text-red-700">{{ $message }}</span>
        @enderror
    </div>
    <div class="flex flex-col flex-1">
        <label for="close_hour">Open tot</label>
        <select name="close_hour" id="close_hour" class="input">
            @foreach(range(1, 24) as $hour)
                <option value="{{ $hour }}" @selected((int) old('close_hour', $exception?->close_hour ?? config('park.opening_hours.close')) === $hour)>
                    {{ $hour }} uur
                </option>
            @endforeach
        </select>
        @error('close_hour')
        <span class="text-red-700">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="flex flex-col">
    <label for="note">Interne notitie (optioneel)</label>
    <input type="text" name="note" id="note" maxlength="100" class="input"
           value="{{ old('note', $exception?->note) }}"
           placeholder="Bijvoorbeeld: Onderhoud. Alleen zichtbaar in het dashboard">
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
            fields.querySelectorAll('select').forEach(select => select.disabled = closed.checked);
        };
        closed.addEventListener('change', toggle);
        toggle();
    })();
</script>
