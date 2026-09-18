<?php

declare(strict_types=1);

namespace Rimba\Workflow\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait HasPintaExecutionRelations
{
    public function ownerTeam(): MorphTo
    {
        return $this->morphTo();
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(config('pinta.models.workflow_output'), 'workflow_instance_id');
    }

    public function childLinks(): HasMany
    {
        return $this->hasMany(config('pinta.models.workflow_link'), 'parent_workflow_instance_id');
    }

    public function parentLinks(): HasMany
    {
        return $this->hasMany(config('pinta.models.workflow_link'), 'child_workflow_instance_id');
    }
}
