<?php
declare(strict_types=1); namespace Rimba\Workflow\Policies;
use Illuminate\Contracts\Auth\Authenticatable; use Rimba\Workflow\Models\WorkflowInstance; use Rimba\Workflow\Services\{DefinitionRegistry,WorkflowAuthorizationService};
final class WorkflowInstancePolicy {public function __construct(private DefinitionRegistry $r,private WorkflowAuthorizationService $a){}public function view(Authenticatable $u,WorkflowInstance $i):bool{return $this->a->view($u,$this->r->workflow($i->definition_slug));}public function perform(Authenticatable $u,WorkflowInstance $i):bool{return $this->a->work($u,$this->r->workflow($i->definition_slug));}}
