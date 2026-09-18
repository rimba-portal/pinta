<?php
declare(strict_types=1); namespace Rimba\Workflow\Actions;
use Illuminate\Database\Eloquent\Model; use Rimba\Workflow\Enums\ActivityStatus; use Rimba\Workflow\Events\ActivityAssigned; use Rimba\Workflow\Models\{ActivityAssignment,ActivityInstance};
final class AssignActivity {public function execute(ActivityInstance $activity,Model $assignee):ActivityAssignment{$assignment=$activity->assignments()->create(['assignee_type'=>$assignee->getMorphClass(),'assignee_id'=>$assignee->getKey(),'assigned_at'=>now()]);$activity->update(['status'=>ActivityStatus::Assigned]);ActivityAssigned::dispatch($assignment);return $assignment;}}
