<x-admin-layout title="Nieuwe uitzondering - Dashboard • VentureValley">
    <div class="flex justify-between items-center pb-3.5">
        <h1 class="text-5xl text-[--color-primary] max-sm:text-4xl">Nieuwe uitzondering</h1>
    </div>
    <section class="max-w-3xl">
        <form action="{{ route('admin.openingstijden.store') }}" method="post"
              class="flex flex-col gap-3 bg-white rounded-lg p-3"
              style="box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.25); corner-shape: squircle">
            @include('admin.openingstijden._form')

            <x-button type="submit" arrow="none" class="w-fit self-end">
                Opslaan
            </x-button>
        </form>
    </section>
</x-admin-layout>
