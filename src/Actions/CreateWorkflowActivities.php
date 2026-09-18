<?php

declare(strict_types=1);

namespace Rimba\Workflow\Actions;

use Rimba\Workflow\Definitions\WorkflowDefinition;
use Rimba\Workflow\Enums\ActivityStatus;
use Rimba\Workflow\Models\WorkflowInstance;

final class CreateWorkflowActivities
{
    public function execute(WorkflowInstance $instance, WorkflowDefinition $definition): void
    {
        foreach ($definition->activities as $a) {
            $instance->activities()->firstOrCreate(['definition_slug' => $a['slug']], ['status' => ActivityStatus::Pending, 'payload' => ['state' => $a['state'], 'title' => $a['title'] ?? $a['slug']]]);
        }
    }
}
