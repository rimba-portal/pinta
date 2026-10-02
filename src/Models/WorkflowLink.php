<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Unguarded]
class WorkflowLink extends Model
{
    public function getTable()
    {
        return config('pinta.tables.workflow_links');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(config('pinta.models.workflow_instance'), 'parent_workflow_instance_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(config('pinta.models.workflow_instance'), 'child_workflow_instance_id');
    }
}
