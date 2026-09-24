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

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    /**
     * Route:
     * /staff/launch-workflow?slug=workflow-slug
     */
    protected static ?string $slug = 'launch-workflow';

    public string $workflowSlug;

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(
        DefinitionRegistry $registry,
        WorkflowAuthorizationService $authorization,
    ): void {
        $slug = request()->query('slug');

        abort_unless(
            filled($slug),
            404,
            'Workflow slug is required.'
        );

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
        //
        // TODO:
        // Resolve subject model
        // app(WorkflowEngine::class)->start(...)
        //
    }
}
