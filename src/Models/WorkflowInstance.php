<?php

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Workflow\Enums\WorkflowStatus;

class WorkflowInstance extends Model
{
    protected $guarded = [];
    protected function casts(): array
    {
        return ['status' => WorkflowStatus::class, 'context' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }
    public function getTable()
    {
        return config('pinta.tables.workflow_instances', parent::getTable());
    }
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
    public function initiator(): MorphTo
    {
        return $this->morphTo();
    }
    public function activities(): HasMany
    {
        return $this->hasMany(config('pinta.models.activity_instance'));
    }
    public function transitions(): HasMany
    {
        return $this->hasMany(config('pinta.models.transition'));
    }
}
