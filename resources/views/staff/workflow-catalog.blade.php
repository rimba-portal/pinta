<x-filament-panels::page
    ><div class="grid gap-4 md:grid-cols-2">
        @foreach ($workflows as $workflow)
            <x-filament::section
                ><x-slot name="heading">
                    {{ $workflow['title'] }}
                </x-slot>
                {{ $workflow['description'] }}
                <x-slot name="footer">
                    <x-filament::button tag="a" :href="url('/workflows/start/'.$workflow['slug'])"
                        >Start</x-filament::button>
                </x-slot></x-filament::section>
        @endforeach</div
></x-filament-panels::page>
