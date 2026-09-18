<?php

declare(strict_types=1);

namespace Rimba\Workflow\Contracts;

use Rimba\Workflow\Definitions\WorkflowDefinition;

interface PermissionSynchronizer
{
    public function sync(WorkflowDefinition $definition, array $permissions): void;

    public function forget(WorkflowDefinition $definition, array $permissions): void;
}
