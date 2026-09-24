<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Workflow\Definitions\WorkflowDefinition;

final class WorkflowAuthorizationService
{
    public function allows(?Authenticatable $user, string $ability, WorkflowDefinition $d): bool
    {
        if (! config('pinta.enforce_permissions')) {
            return true;
        }

        return $user instanceof Authenticatable && method_exists($user, 'can') && $user->can($d->permission($ability));
    }

    public function initiate(?Authenticatable $u, WorkflowDefinition $d): bool
    {
        if (! config('pinta.enforce_permissions')) {
            return true;
        }

        return $this->allows($u, 'init', $d);
    }

    public function work(?Authenticatable $u, WorkflowDefinition $d): bool
    {
        if (! config('pinta.enforce_permissions')) {
            return true;
        }

        return $this->allows($u, 'work', $d);
    }

    public function view(?Authenticatable $u, WorkflowDefinition $d): bool
    {
        if (! config('pinta.enforce_permissions')) {
            return true;
        }

        return $this->allows($u, 'view', $d) || $this->own($u, $d);
    }

    public function own(?Authenticatable $u, WorkflowDefinition $d): bool
    {
        if (! config('pinta.enforce_permissions')) {
            return true;
        }

        return $this->allows($u, 'own', $d);
    }
}
