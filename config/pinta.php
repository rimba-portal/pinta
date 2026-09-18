<?php

declare(strict_types=1);
use Rimba\Workflow\Models\ActivityInstance;
use Rimba\Workflow\Models\Transition;
use Rimba\Workflow\Models\WorkflowInstance;

return [
    'definitions_path' => base_path('definitions'),
    'tables' => [
        'workflow_instances' => 'work_workflow_instances',
        'activity_instances' => 'work_activity_instances',
        'transitions' => 'work_transitions',
    ],
    'models' => [
        'workflow_instance' => WorkflowInstance::class,
        'activity_instance' => ActivityInstance::class,
        'transition' => Transition::class,
    ],
];
