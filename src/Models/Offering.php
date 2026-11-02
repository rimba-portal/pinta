<?php

declare(strict_types=1);

namespace Rimba\Request\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Packstub\Flow\Models\Workflow;
use Packstub\FormBuilder\Models\Form;

#[Table(name: 'req_offerings')]
#[Fillable([
    'code',
    'name',
    'description',
    'org_unit_id',
    'org_team_id',
    'form_id',
    'workflow_id',
    'visibility',
    'availability',
    'icon',
    'color',
    'sort',
    'settings',
])]
class Offering extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'sort' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(
            Form::class,
            'form_id',
        );
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(
            Workflow::class,
            'workflow_id',
        );
    }

    public function requests(): HasMany
    {
        return $this->hasMany(
            Request::class,
            'offering_id',
        );
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.request.models.org_unit'),
            'org_unit_id',
        );
    }

    public function orgTeam(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.request.models.org_team'),
            'org_team_id',
        );
    }
}
