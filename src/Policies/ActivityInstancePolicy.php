<?php
declare(strict_types=1); namespace Rimba\Workflow\Policies;
use Illuminate\Contracts\Auth\Authenticatable; use Rimba\Workflow\Models\ActivityInstance;
final class ActivityInstancePolicy {public function perform(Authenticatable $u,ActivityInstance $a):bool{return $a->assignments()->whereMorphedTo('assignee',$u)->whereNull('completed_at')->exists();}}
