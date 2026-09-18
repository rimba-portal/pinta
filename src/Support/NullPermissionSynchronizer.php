<?php

declare(strict_types=1);

namespace Rimba\Workflow\Support;

use Rimba\Workflow\Contracts\PermissionSynchronizer;
use Rimba\Workflow\Definitions\WorkflowDefinition;

final class NullPermissionSynchronizer implements PermissionSynchronizer
{
    public function sync(WorkflowDefinition $definition, array $permissions): void {}

    public function forget(WorkflowDefinition $definition, array $permissions): void {}
}
