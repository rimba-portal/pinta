<?php
return [
    'definitions_path' => base_path('definitions'),
    'tables' => [
        'workflow_instances' => 'work_workflow_instances',
        'activity_instances' => 'work_activity_instances',
        'transitions' => 'work_transitions',
    ],
    'models' => [
        'workflow_instance' => Rimba\Workflow\Models\WorkflowInstance::class,
        'activity_instance' => Rimba\Workflow\Models\ActivityInstance::class,
        'transition' => Rimba\Workflow\Models\Transition::class,
    ],
];