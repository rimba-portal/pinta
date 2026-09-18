<?php
declare(strict_types=1); namespace Rimba\Workflow\Services;
use Illuminate\Contracts\Auth\Authenticatable; use Illuminate\Support\Collection;
final class WorkflowCatalogService {public function __construct(private DefinitionRegistry $registry,private WorkflowAuthorizationService $auth){}public function initiableBy(?Authenticatable $user):Collection{return $this->registry->workflows()->filter(fn($d)=>$this->auth->initiate($user,$d))->values();}public function ownedBy(?Authenticatable $user):Collection{return $this->registry->workflows()->filter(fn($d)=>$this->auth->own($user,$d))->values();}}
