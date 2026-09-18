<?php

declare(strict_types=1);

namespace Rimba\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Rimba\Workflow\Models\Transition;
use Rimba\Workflow\Models\WorkflowInstance;

class WorkflowTransitioned
{
    use Dispatchable;

    public function __construct(public WorkflowInstance $instance, public Transition $transition) {}
}
