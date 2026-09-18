<?php

declare(strict_types=1);

namespace Rimba\Workflow\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rimba\Workflow\Services\DefinitionRegistry;
use Rimba\Workflow\Services\WorkflowPermissionService;

#[Description('Synchronize permissions for all Pinta workflows')]
#[Signature('pinta:permissions')]
final class SyncWorkflowPermissions extends Command
{
    public function handle(DefinitionRegistry $r, WorkflowPermissionService $p): int
    {
        foreach ($r->workflows() as $d) {
            foreach ($p->sync($d) as $n) {
                $this->line($n);
            }
        }

        return self::SUCCESS;
    }
}
