<x-admin-layout title="Openingstijden - Dashboard • VentureValley">
    @if (session('alert'))
        <div class="alert">
            {{ session('alert') }}
        </div>
    @endif

    <section>
        <div class="flex justify-between items-center pb-3.5">
            <h1 class="text-5xl text-[--color-primary] max-sm:text-4xl">Openingstijden</h1>
            <x-button url="{{ route('admin.openingstijden.create') }}" arrow="none">
                Nieuwe uitzondering
            </x-button>
        </div>
        <div class="flex justify-between items-center gap-3 pb-3.5">
            <p>
                Standaard is het park elke dag geopend van
                <strong>{{ config('park.opening_hours.open') }}</strong> tot
                <strong>{{ config('park.opening_hours.close') }} uur</strong>. Hieronder staan de afwijkende dagen.
            </p>
            @if($showPast)
                <a href="{{ route('admin.openingstijden.index') }}" class="text-[--color-primary] shrink-0">
                    Verberg verleden
                </a>
            @else
                <a href="{{ route('admin.openingstijden.index', ['verleden' => 1]) }}"
                   class="text-[--color-primary] shrink-0">
                    Toon verleden
                </a>
            @endif
        </div>
        <table class="bg-white rounded-lg min-w-full"
               style="box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.25); corner-shape: squircle">
            <thead class="text-left font-semibold border-b-2">
            <tr>
                <th class="p-3">Datum</th>
                <th class="p-3">Openingstijden</th>
                <th class="max-sm:hidden p-3">Interne notitie</th>
                <th class="p-3"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($exceptions as $exception)
                <tr @class(['border-b', 'text-gray-400' => $exception->date->isPast() && !$exception->date->isToday()])>
                    <td class="p-3">{{ ucfirst($exception->date->locale('nl')->translatedFormat('D d-m-Y')) }}</td>
                    <td class="p-3">
                        @if($exception->is_closed)
                            <span class="text-red-700">Gesloten</span>
                        @else
                            {{ $exception->open_hour }} – {{ $exception->close_hour }} uur
                        @endif
                    </td>
                    <td class="max-sm:hidden p-3">{{ $exception->note }}</td>
                    <td class="flex justify-end gap-5 text-right p-3">
                        <a href="{{ route('admin.openingstijden.edit', $exception) }}" aria-label="Bewerken"><i
                                class="fa-solid fa-pencil"></i></a>
                        <form action="{{ route('admin.openingstijden.destroy', $exception) }}" method="post"
                              onsubmit="return confirm(`Weet je zeker dat je de uitzondering op {{ $exception->date->format('d-m-Y') }} wilt verwijderen?\nDe standaard openingstijden gelden dan weer.`)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Verwijderen">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-3 text-gray-400 italic">Er zijn geen uitzonderingen.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-admin-layout>
