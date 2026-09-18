<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rimba\Workflow\Enums\ActivityStatus;

class ActivityInstance extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => ActivityStatus::class, 'payload' => 'array', 'due_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function getTable()
    {
        return config('pinta.tables.activity_instances', parent::getTable());
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(config('pinta.models.workflow_instance'), 'workflow_instance_id');
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }
}
