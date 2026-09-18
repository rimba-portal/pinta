<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Rimba\Workflow\Definitions\WorkflowDefinition;

final class WorkflowCatalogService
{
    public function __construct(private DefinitionRegistry $definitionRegistry, private WorkflowAuthorizationService $workflowAuthorizationService) {}

    public function initiableBy(?Authenticatable $user): Collection
    {
        return $this->definitionRegistry->workflows()->filter(fn (WorkflowDefinition $d): bool => $this->workflowAuthorizationService->initiate($user, $d))->values();
    }

    public function ownedBy(?Authenticatable $user): Collection
    {
        return $this->definitionRegistry->workflows()->filter(fn (WorkflowDefinition $d): bool => $this->workflowAuthorizationService->own($user, $d))->values();
    }
}
