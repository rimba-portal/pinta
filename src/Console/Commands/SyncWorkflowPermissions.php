<?php
declare(strict_types=1); namespace Rimba\Workflow\Console\Commands;
use Illuminate\Console\Command; use Rimba\Workflow\Services\{DefinitionRegistry,WorkflowPermissionService};
final class SyncWorkflowPermissions extends Command {protected $signature='pinta:permissions';protected $description='Synchronize permissions for all Pinta workflows';public function handle(DefinitionRegistry $r,WorkflowPermissionService $p):int{foreach($r->workflows() as $d){foreach($p->sync($d) as $n)$this->line($n);}return self::SUCCESS;}}
