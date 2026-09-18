<?php

declare(strict_types=1);

namespace Rimba\Workflow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Rimba\Workflow\Models\WorkflowInstance;

final class WorkflowRejected
{
    use Dispatchable;

    public function __construct(public WorkflowInstance $instance) {}
}
