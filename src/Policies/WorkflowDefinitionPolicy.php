<?php
declare(strict_types=1); namespace Rimba\Workflow\Policies;
use Illuminate\Contracts\Auth\Authenticatable; use Rimba\Workflow\Definitions\WorkflowDefinition; use Rimba\Workflow\Services\WorkflowAuthorizationService;
final class WorkflowDefinitionPolicy {public function __construct(private WorkflowAuthorizationService $auth){}public function view(Authenticatable $u,WorkflowDefinition $d):bool{return $this->auth->view($u,$d);}public function create(Authenticatable $u,WorkflowDefinition $d):bool{return $this->auth->own($u,$d);}public function update(Authenticatable $u,WorkflowDefinition $d):bool{return $this->auth->own($u,$d);}public function initiate(Authenticatable $u,WorkflowDefinition $d):bool{return $this->auth->initiate($u,$d);}}
