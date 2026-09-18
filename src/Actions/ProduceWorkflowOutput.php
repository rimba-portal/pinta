<?php

declare(strict_types=1);

namespace Rimba\Workflow\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Workflow\Models\WorkflowInstance;
use Rimba\Workflow\Models\WorkflowOutput;

final class ProduceWorkflowOutput
{
    public function execute(WorkflowInstance $w, string $key, mixed $value = null, ?Model $record = null): WorkflowOutput
    {
        return $w->outputs()->updateOrCreate(['key' => $key], ['value' => $value, 'record_type' => $record?->getMorphClass(), 'record_id' => $record?->getKey(), 'produced_at' => now()]);
    }
}
