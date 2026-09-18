<?php

declare(strict_types=1);

namespace Rimba\Workflow\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Workflow\Models\WorkflowInstance;
use Rimba\Workflow\Services\DefinitionRegistry;
use Rimba\Workflow\Services\WorkflowAuthorizationService;

final class WorkflowInstancePolicy
{
    public function __construct(private DefinitionRegistry $definitionRegistry, private WorkflowAuthorizationService $workflowAuthorizationService) {}

    public function view(Authenticatable $u, WorkflowInstance $i): bool
    {
        return $this->workflowAuthorizationService->view($u, $this->definitionRegistry->workflow($i->definition_slug));
    }

    public function perform(Authenticatable $u, WorkflowInstance $i): bool
    {
        return $this->workflowAuthorizationService->work($u, $this->definitionRegistry->workflow($i->definition_slug));
    }
}
