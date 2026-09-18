<?php

namespace Rimba\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Rimba\Workflow\Models\WorkflowInstance;
use Rimba\Workflow\Models\Transition;

class WorkflowTransitioned
{
    use Dispatchable;
    public function __construct(public WorkflowInstance $instance, public Transition $transition) {}
}
