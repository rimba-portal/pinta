<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">{{ $this->getRecord()->name }}</h2>

            @if ($this->getRecord()->description)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $this->getRecord()->description }}</p>
            @endif
        </div>

        <livewire:form-renderer :form-id="$this->getRecord()->form_id" />
    </div>
</x-filament-panels::page>
