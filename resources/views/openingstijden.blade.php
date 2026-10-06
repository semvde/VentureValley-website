<x-default-layout title="Openingstijden • VentureValley">
    @push('head')
        <meta name="description"
              content="Ben jij er klaar voor om hét virtuele dagje uit in Minecraft zelf te ervaren? Hier vind je alle informatie over onze openingstijden!">
    @endpush

    <x-slot name="header">
        <x-header height="medium" image="{{ asset('/images/EntranceWithForest.webp') }}">
            <h1>Openingstijden</h1>
        </x-header>
    </x-slot>

    <section class="flex flex-col gap-8 text-center pt-14">
        <div>
            <h2>Eindeloos plezier!</h2>
            <p class="text-lg">Ben jij er klaar voor om hét virtuele dagje uit in Minecraft zelf te ervaren? Hier vind
                je alle informatie over onze openingstijden!</p>
        </div>
        <hr>
    </section>

    <section class="grid grid-cols-1 gap-5 md:grid-cols-2 pt-8 pb-14">
        <div id="kalender">
            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 pb-5">
                @if($previousMonth)
                    <a href="{{ route('openingstijden', ['maand' => $previousMonth->format('Y-m')]) }}#kalender"
                       class="text-[--color-primary] font-semibold"
                       aria-label="Vorige maand">
                        <i class="fa-solid fa-angle-left"></i>
                        <span
                            class="max-sm:hidden">{{ ucfirst($previousMonth->locale('nl')->translatedFormat('F')) }}</span>
                    </a>
                @else
                    <span></span>
                @endif

                <h3 class="mb-0 text-center">{{ ucfirst($month->locale('nl')->translatedFormat('F Y')) }}</h3>

                @if($nextMonth)
                    <a href="{{ route('openingstijden', ['maand' => $nextMonth->format('Y-m')]) }}#kalender"
                       class="text-[--color-primary] font-semibold justify-self-end"
                       aria-label="Volgende maand">
                        <span
                            class="max-sm:hidden">{{ ucfirst($nextMonth->locale('nl')->translatedFormat('F')) }}</span>
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                @else
                    <span></span>
                @endif
            </div>

            {{-- Desktop: maandkalender --}}
            <div class="grid grid-cols-7 gap-2 max-md:hidden">
                @foreach(['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'] as $weekday)
                    <div class="text-center font-semibold pb-1">{{ $weekday }}</div>
                @endforeach

                @foreach($weeks->flatten(1) as $day)
                    @if($day === null)
                        <div></div>
                    @else
                        @php
                            $classes = \Illuminate\Support\Arr::toCssClasses([
                                'flex flex-col gap-1 rounded-lg p-2 border-2 text-center',
                                'bg-gray-100 text-gray-400 border-transparent' => $day['date']->isPast() && !$day['date']->isToday(),
                                'bg-white border-transparent' => !$day['date']->isPast(),
                                'bg-white border-[--color-primary]' => $day['date']->isToday(),
                            ]);
                        @endphp
                        <div class="{{ $classes }}"
                             style="box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1); corner-shape: scoop">
                            <span class="font-semibold">{{ $day['date']->day }}</span>
                            @if($day['is_closed'])
                                <span class="text-red-700 text-xs">Gesloten</span>
                            @else
                                <span @class(['text-xs', 'text-yellow-700 font-semibold' => $day['is_exception']])>
                                    {{ $day['open'] }}u – {{ $day['close'] }}u
                                </span>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Mobiel: lijst --}}
            <ul class="flex flex-col gap-2 md:hidden">
                @foreach($weeks->flatten(1)->filter() as $day)
                    @php
                        $classes = \Illuminate\Support\Arr::toCssClasses([
                            'flex justify-between items-center gap-3 rounded-lg px-3 py-2 border-2',
                            'bg-gray-100 text-gray-400 border-transparent' => $day['date']->isPast() && !$day['date']->isToday(),
                            'bg-white border-transparent' => !$day['date']->isPast(),
                            'bg-white border-[--color-primary]' => $day['date']->isToday(),
                        ]);
                    @endphp
                    <li class="{{ $classes }}"
                        style="box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1); corner-shape: scoop">
                        <span
                            class="font-semibold">{{ ucfirst($day['date']->locale('nl')->translatedFormat('l j F')) }}</span>
                        @if($day['is_closed'])
                            <span class="text-red-700">Gesloten</span>
                        @else
                            <span @class(['text-yellow-700 font-semibold' => $day['is_exception']])>
                                {{ $day['open'] }}u – {{ $day['close'] }}u
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="flex flex-col items-center justify-center text-center">
            <h3>Kom langs!</h3>
            <p>VentureValley is dagelijks te bezoeken van {{ config('park.opening_hours.open') }}:00
                tot {{ config('park.opening_hours.close') }}:00. Uitzonderingen staan in de kalender aangegeven. 's
                Nachts is VentureValley gesloten. Vaak voeren wij dan onderhoud uit of implementeren we nieuwe updates!
                Ook zijn er dan vaak geen Teamleden aanwezig om eventuele problemen op te lossen en de goede orde in het
                park te bewaren.</p>
            <img src="{{asset('/images/EntranceGates.webp')}}" alt="" class="rounded-lg mt-2.5 md:w-4/5 "
                 style="corner-shape: squircle">
        </div>
    </section>
</x-default-layout>
