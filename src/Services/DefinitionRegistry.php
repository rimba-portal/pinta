<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Illuminate\Support\Collection;
use Rimba\Workflow\Definitions\WorkflowDefinition as RuntimeWorkflowDefinition;
use Rimba\Workflow\Models\WorkflowDefinition;

final class DefinitionRegistry
{
    public function __construct(
        private readonly WorkflowDefinitionRepository $workflowDefinitionRepository,
    ) {}

    public function workflow(string $slug): RuntimeWorkflowDefinition
    {
        $workflowDefinition = $this->workflowDefinitionRepository->find($slug);

        return RuntimeWorkflowDefinition::fromArray(
            $workflowDefinition->toDefinitionArray()
        );
    }

    /**
     * @return Collection<int, RuntimeWorkflowDefinition>
     */
    public function workflows(): Collection
    {
        return $this->workflowDefinitionRepository
            ->all()
            ->map(
                static fn (
                    WorkflowDefinition $model
                ): RuntimeWorkflowDefinition => RuntimeWorkflowDefinition::fromArray(
                    $model->toDefinitionArray()
                )
            )
            ->sortBy('title')
            ->values();
    }
}
