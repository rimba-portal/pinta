<?php

declare(strict_types=1);

namespace Rimba\Workflow\Services;

use Rimba\Workflow\Contracts\PermissionSynchronizer;
use Rimba\Workflow\Definitions\WorkflowDefinition;

final class WorkflowPermissionService
{
    public const ABILITIES = ['init', 'work', 'view', 'own'];

    public function __construct(private PermissionSynchronizer $permissionSynchronizer) {}

    public function names(WorkflowDefinition $d): array
    {
        return array_map(fn (string $a): string => $d->permission($a), self::ABILITIES);
    }

    public function sync(WorkflowDefinition $d): array
    {
        $names = $this->names($d);
        $this->permissionSynchronizer->sync($d, $names);

        return $names;
    }
}
