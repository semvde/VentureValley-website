<x-default-layout title="{{ $ride->name }} • VentureValley">
    @push('head')
        <meta name="description" content="{{ $ride->description }}">
    @endpush

    <x-slot name="header">
        <x-header height="large" image="{{ asset('storage/' . $ride->background_image) }}">
            @if(!$ride->public)
                <div class="mb-3">Let op! Deze attractie is <span class="text-red-700">verborgen</span>!</div>
            @endif
            <span class="font-[Amaranth] text-lg">{{ strtoupper($ride->name) }} ✦ {{ strtoupper($ride->type) }}</span>
            <h1>{{ $ride->subtitle }}</h1>
            <x-button variant="transparent" url="{{ route('attracties.index') }}" arrow="left" class="mt-5">
                Terug naar overzicht
            </x-button>
        </x-header>
    </x-slot>

    @php
        $stats = collect([
            ['title' => 'Snelheid', 'value' => $ride->stat_speed, 'unit' => 'km/u'],
            ['title' => 'Baanlengte', 'value' => $ride->stat_length, 'unit' => 'meter'],
            ['title' => 'Hoogte', 'value' => $ride->stat_height, 'unit' => 'meter'],
            ['title' => 'Ritduur', 'value' => $ride->stat_duration, 'unit' => 'minuten'],
            ['title' => 'Capaciteit', 'value' => $ride->stat_capacity, 'unit' => 'p/u'],
        ])->filter(fn($stat) => filled($stat['value']))
          ->take(4)
          ->map(function ($stat) {
              if ($stat['title'] === 'Ritduur') {
                  $minutes = intdiv($stat['value'], 60);
                  $seconds = $stat['value'] % 60;
                  $stat['value'] = "{$minutes}:" . str_pad($seconds, 2, '0', STR_PAD_LEFT);
              }
              return $stat;
          });
    @endphp

    <x-header-card>
        <div class="flex gap-10">
            @foreach($stats as $stat)
                <div class="flex flex-col">
                    <span
                        class="font-[Amaranth] font-bold text-2xl text-[--color-primary] max-sm:text-sm">{{ $stat['title'] }}</span>
                    <span
                        class="font-semibold text-7xl text-[--color-primary] max-sm:text-3xl">{{ $stat['value'] }}</span>
                    <span class="font-[Amaranth] text-lg max-sm:text-sm">{{ $stat['unit'] }}</span>
                </div>
            @endforeach
        </div>
    </x-header-card>

    <section class="text-center py-14">
        <h2>{{ $ride->tagline }}</h2>
        <p>{{ $ride->description }}</p>
    </section>

    <section class="text-center pb-14">
        <h2>Goed om te weten</h2>
        <div class="grid grid-cols-3 gap-5 max-sm:grid-cols-1 bg-white p-5 rounded-lg"
             style="box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.25); corner-shape: squircle">
            @if($ride->property_controllable)
                <div class="flex gap-5 items-center">
                    <img src="{{ asset('/images/IconControllable.png') }}" alt="" class="w-20">
                    <p class="text-sm">Deze attractie is bestuurbaar door VIP's. Open poortjes, sluit beugels, geef
                        treinen vrij en
                        meer.</p>
                </div>
            @endif
            @if($ride->property_audio)
                <div class="flex gap-5 items-center">
                    <img src="{{ asset('/images/IconAudio.png') }}" alt="" class="w-20">
                    <p class="text-sm">Wanneer je verbonden bent met onze Audio Server, kun je in en om deze attractie
                        muziek en geluid
                        horen!</p>
                </div>
            @endif
            @if($ride->property_smoothcoasters)
                <div class="flex gap-5 items-center">
                    <img src="{{ asset('/images/IconSmoothcoasters.png') }}" alt="" class="w-20">
                    <p class="text-sm">Als je de <a class="text-[--color-primary] underline"
                                                    href="{{ route('bezoeken') }}#smoothcoasters">SmoothCoasters-mod</a>
                        gebruikt, beweegt je camera mee met de trein in bijvoorbeeld loopings.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="text-center pb-14">
        <h2>Ontdek meer attracties</h2>
        <div class="grid grid-cols-3 gap-5 max-sm:grid-cols-1 lg:px-20">
            @forelse($rides as $ride)
                <a href="{{ route('attracties.show', $ride->slug) }}">
                    <article
                        class="flex flex-col justify-end min-h-[400px] bg-center bg-no-repeat bg-cover rounded-lg text-center pb-4 px-1 transition-all duration-300 hover:bg-bottom"
                        style="background-image: linear-gradient(180deg, #00000000 70%, #000000 100%), url({{ asset('storage/' . $ride->list_image) }}); box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.5); corner-shape: scoop">
                        <span class="text-white">{{ $ride->type }}</span>
                        <h3 class="text-4xl text-white">{{ $ride->name }}</h3>
                    </article>
                </a>
            @empty
                <p class="col-span-3">Er zijn nu geen andere attracties om te ontdekken! Kom binnenkort terug!</p>
            @endforelse
        </div>
    </section>
</x-default-layout>
