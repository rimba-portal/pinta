<?php

declare(strict_types=1);

namespace Rimba\Request\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table(name: 'req_model_states')]
#[Fillable(['state'])]
class ModelState extends Model
{
    protected function casts(): array
    {
        return [
            'state' => ModelState::class,
        ];
    }

    public function stateable(): MorphTo
    {
        return $this->morphTo();
    }
}
