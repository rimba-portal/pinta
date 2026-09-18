<?php

declare(strict_types=1);

use Rimba\Workflow\Models\ActivityAssignment;
use Rimba\Workflow\Models\ActivityInstance;
use Rimba\Workflow\Models\Transition;
use Rimba\Workflow\Models\WorkflowInstance;
use Rimba\Workflow\Models\WorkflowLink;
use Rimba\Workflow\Models\WorkflowOutput;
use Rimba\Workflow\Support\NullPermissionSynchronizer;

return [
    'definitions_path' => base_path('definitions'),
    'permission_synchronizer' => NullPermissionSynchronizer::class,
    'tables' => [
        'workflow_instances' => 'work_workflow_instances',
        'activity_instances' => 'work_activity_instances',
        'activity_assignments' => 'work_activity_assignments',
        'transitions' => 'work_transitions',
        'workflow_links' => 'work_workflow_links',
        'workflow_outputs' => 'work_workflow_outputs',
    ],
    'models' => [
        'workflow_instance' => WorkflowInstance::class,
        'activity_instance' => ActivityInstance::class,
        'activity_assignment' => ActivityAssignment::class,
        'transition' => Transition::class,
        'workflow_link' => WorkflowLink::class,
        'workflow_output' => WorkflowOutput::class,
    ],
];
