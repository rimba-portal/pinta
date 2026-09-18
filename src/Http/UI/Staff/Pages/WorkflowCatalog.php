<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Staff\Pages;

use Filament\Pages\Page;
use Rimba\Workflow\Services\WorkflowCatalogService;

class WorkflowCatalog extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected string $view = 'pinta::staff.workflow-catalog';

    public array $workflows = [];

    public function mount(WorkflowCatalogService $catalog): void
    {
        $this->workflows = $catalog->initiableBy(auth()->user())->map(fn ($d): array => ['slug' => $d->slug, 'title' => $d->title, 'description' => $d->raw['description'] ?? null])->all();
    }
}
