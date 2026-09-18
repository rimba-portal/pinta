<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowOutput extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'array', 'produced_at' => 'datetime'];
    }

    public function getTable()
    {
        return config('pinta.tables.workflow_outputs');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(config('pinta.models.workflow_instance'), 'workflow_instance_id');
    }

    public function record(): MorphTo
    {
        return $this->morphTo();
    }
}
