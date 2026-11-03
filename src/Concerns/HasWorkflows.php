<?php

declare(strict_types=1);

namespace Rimba\Request\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Rimba\Request\Concerns\HasWorkflows as PackstubHasWorkflows;
use Rimba\Request\Enums\State;
use Rimba\Request\Models\ModelState;

trait HasWorkflows
{
    use PackstubHasWorkflows;

    public function state(): MorphOne
    {
        return $this->morphOne(ModelState::class, 'stateable');
    }

    public function currentModelState(): State
    {
        return $this->state?->state ?? State::Draft;
    }

    public function setModelState(State $state): static
    {
        $this->state()->updateOrCreate([], ['state' => $state]);
        unset($this->relations['state']);

        return $this;
    }

    public function isModelState(State $state): bool
    {
        return $this->currentModelState() === $state;
    }
}
