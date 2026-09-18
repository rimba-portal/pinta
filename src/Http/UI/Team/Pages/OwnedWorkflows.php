<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Team\Pages;

use Filament\Pages\Page;
use Rimba\Workflow\Services\WorkflowCatalogService;

class OwnedWorkflows extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected string $view = 'pinta::team.owned-workflows';

    public array $workflows = [];

    public function mount(WorkflowCatalogService $catalog): void
    {
        $this->workflows = $catalog->ownedBy(auth()->user())->map(fn ($d) => $d->raw)->all();
    }
}
