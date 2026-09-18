<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityAssignment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'claimed_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function getTable()
    {
        return config('pinta.tables.activity_assignments');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(config('pinta.models.activity_instance'), 'activity_instance_id');
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }
}
