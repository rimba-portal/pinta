<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rimba\Workflow\Enums\WorkflowAction;

#[WithoutTimestamps]
class Transition extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['action' => WorkflowAction::class, 'payload' => 'array', 'performed_at' => 'datetime'];
    }

    public function getTable()
    {
        return config('pinta.tables.transitions', parent::getTable());
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(config('pinta.models.workflow_instance'), 'workflow_instance_id');
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
