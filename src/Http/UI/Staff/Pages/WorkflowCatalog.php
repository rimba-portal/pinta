<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Staff\Pages;

use Filament\Pages\Page;
use Rimba\Workflow\Services\WorkflowCatalogService;

class WorkflowCatalog extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static string|\BackedEnum|null $navigationIcon = 'bites-w-request';

    protected static ?string $navigationLabel = 'Request for something';

    protected static ?int $navigationSort = 32;

    protected static ?string $title = 'Request for something';

    protected ?string $subheading = 'Request for support, service, item, asset, equipment, etc. through workflow system.';

    protected string $view = 'bites::staff.workflow-catalog';

    public array $workflows = [];

    public function mount(
        WorkflowCatalogService $catalog
    ): void {
        $this->workflows = $catalog
            ->initiableBy(auth()->user())
            ->map(fn ($d): array => [
                'slug' => $d->slug,
                'title' => $d->title,
                'description' => $d->raw['description'] ?? null,
                'url' => LaunchWorkflow::getUrl([
                    'slug' => $d->slug,
                ]),
            ])
            ->all();
    }
}
