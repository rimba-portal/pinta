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

    protected string $view = 'pinta::staff.launch-workflow';

    protected static ?string $slug = null;

    public ?array $data = [];

    public function mount(string $slug, DefinitionRegistry $r, WorkflowAuthorizationService $a): void
    {
        $workflowDefinition = $r->workflow($slug);
        abort_unless($a->initiate(auth()->user(), $workflowDefinition), 403);
        $this->slug = $slug;
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        $workflowDefinition = app(DefinitionRegistry::class)->workflow($this->slug);

        return app(LaunchFormSchemaService::class)->build($workflowDefinition->startForm);
    }

    public function submit(): void
    {/* Host form must resolve/create the domain subject, then call WorkflowEngine::start(). */
    }
}
