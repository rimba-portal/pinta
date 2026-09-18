<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rimba\Workflow\Models\ActivityAssignment;

final class TaskInboxService
{
    public function queryFor(Model $user): Builder
    {
        return ActivityAssignment::query()->with(['activity.workflow'])->whereMorphedTo('assignee', $user)->whereNull('completed_at')->latest('assigned_at');
    }
}
