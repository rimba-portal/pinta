<?php

declare(strict_types=1);

namespace Rimba\Workflow\Actions;

use Rimba\Workflow\Models\WorkflowInstance;
use Rimba\Workflow\Models\WorkflowLink;

final class LinkWorkflows
{
    public function execute(WorkflowInstance $parent, WorkflowInstance $child, string $event): WorkflowLink
    {
        return WorkflowLink::firstOrCreate(['parent_workflow_instance_id' => $parent->getKey(), 'child_workflow_instance_id' => $child->getKey()], ['trigger_event' => $event]);
    }
}
