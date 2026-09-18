<?php

namespace Rimba\Workflow\Contracts;

use Illuminate\Database\Eloquent\Model;
use Rimba\Workflow\Enums\WorkflowAction;
use Rimba\Workflow\Models\WorkflowInstance;

interface WorkflowEngine
{
    public function start(string $definitionSlug, Model $subject, ?Model $initiator = null, array $context = []): WorkflowInstance;
    public function perform(WorkflowInstance $instance, WorkflowAction|string $action, ?Model $actor = null, array $payload = []): WorkflowInstance;
    public function availableActions(WorkflowInstance $instance, ?Model $actor = null): array;
}
