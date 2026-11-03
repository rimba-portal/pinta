<?php

declare(strict_types=1);

namespace Rimba\Request\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Packstub\Flow\Models\WorkflowRun;
use Packstub\FormBuilder\Models\FormSubmission;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\People\Models\Staff;
use Rimba\Request\Concerns\HasWorkflows;

#[Table(name: 'req_requests')]
#[Fillable([
    'number',
    'offering_id',
    'requested_by',
    'org_unit_id',
    'org_team_id',
    'phase_code',
    'form_submission_id',
    'workflow_run_id',
    'subject_type',
    'subject_id',
    'context',
    'submitted_at',
    'completed_at',
    'cancelled_at',
])]
class Request extends Model
{
    use HasUuids;
    use HasWorkflows;

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(
            Offering::class,
            'offering_id',
        );
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            Staff::class,
            'requested_by',
        );
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(
            OrgUnit::class,
            'org_unit_id',
        );
    }

    public function orgTeam(): BelongsTo
    {
        return $this->belongsTo(
            OrgTeam::class,
            'org_team_id',
        );
    }

    public function formSubmission(): BelongsTo
    {
        return $this->belongsTo(
            FormSubmission::class,
            'form_submission_id',
        );
    }

    public function workflowRun(): BelongsTo
    {
        return $this->belongsTo(
            WorkflowRun::class,
            'workflow_run_id',
        );
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
