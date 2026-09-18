<x-filament-panels::page
    ><div class="space-y-4">
        @forelse ($tasks as $task)
            <x-filament::section>{{ data_get($task,'activity.payload.title',data_get($task,'activity.definition_slug')) }}</x-filament::section>
        @empty
            <x-filament::section>No open tasks.</x-filament::section>
        @endforelse</div
></x-filament-panels::page>
