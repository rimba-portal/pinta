<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Staff\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Rimba\Workflow\Services\DefinitionRegistry;
use Rimba\Workflow\Services\LaunchFormSchemaService;
use Rimba\Workflow\Services\WorkflowAuthorizationService;

class LaunchWorkflow extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'bites::staff.launch-workflow';

    /**
     * Filament route:
     * /staff/launch-workflow/{slug}
     */
    protected static ?string $slug = 'launch-workflow/{slug}';

    /**
     * Workflow definition slug.
     */
    public string $workflowSlug;

    public ?array $data = [];

    public function mount(
        string $slug,
        DefinitionRegistry $registry,
        WorkflowAuthorizationService $authorization
    ): void {
        $workflowDefinition = $registry->workflow($slug);

        abort_unless(
            $authorization->initiate(
                auth()->user(),
                $workflowDefinition
            ),
            403
        );

        $this->workflowSlug = $slug;

        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        $workflowDefinition = app(DefinitionRegistry::class)
            ->workflow($this->workflowSlug);

        return app(LaunchFormSchemaService::class)
            ->build($workflowDefinition->startForm);
    }

    public function submit(): void
    {
        // TODO:
        // resolve domain model
        // WorkflowEngine::start(...)
    }
}
