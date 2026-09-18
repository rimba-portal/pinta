<?php

namespace Rimba\Workflow\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Workflow\Contracts\WorkflowEngine as Contract;
use Rimba\Workflow\Enums\{WorkflowAction, WorkflowStatus};
use Rimba\Workflow\Events\{WorkflowStarted, WorkflowTransitioned};
use Rimba\Workflow\Exceptions\InvalidTransition;
use Rimba\Workflow\Models\WorkflowInstance;

class WorkflowEngine implements Contract
{
  public function __construct(private DefinitionRepository $definitions) {}
  public function start(string $definitionSlug, Model $subject, ?Model $initiator = null, array $context = []): WorkflowInstance
  {
    $d = $this->definitions->get($definitionSlug);
    $initial = $d['initial_state'] ?? array_key_first($d['states']);
    $i = WorkflowInstance::create(['definition_slug' => $definitionSlug, 'definition_version' => $d['version'] ?? 1, 'subject_type' => $subject->getMorphClass(), 'subject_id' => $subject->getKey(), 'initiator_type' => $initiator?->getMorphClass(), 'initiator_id' => $initiator?->getKey(), 'current_state' => $initial, 'status' => WorkflowStatus::Draft, 'context' => $context, 'started_at' => now()]);
    WorkflowStarted::dispatch($i);
    return $i;
  }
  public function perform(WorkflowInstance $instance, WorkflowAction|string $action, ?Model $actor = null, array $payload = []): WorkflowInstance
  {
    $action = $action instanceof WorkflowAction ? $action : WorkflowAction::from($action);
    $d = $this->definitions->get($instance->definition_slug);
    $t = collect($d['transitions'] ?? [])->first(fn($t) => $t['from'] === $instance->current_state && $t['action'] === $action->value);
    if (!$t) throw new InvalidTransition("Action [{$action->value}] is unavailable from [{$instance->current_state}].");
    return DB::transaction(function () use ($instance, $action, $actor, $payload, $t) {
      $from = $instance->current_state;
      $to = $t['to'];
      $status = $this->statusFor($to, $action);
      $instance->update(['current_state' => $to, 'status' => $status, 'completed_at' => $status === WorkflowStatus::Completed ? now() : null]);
      $log = $instance->transitions()->create(['from_state' => $from, 'to_state' => $to, 'action' => $action, 'actor_type' => $actor?->getMorphClass(), 'actor_id' => $actor?->getKey(), 'payload' => $payload, 'performed_at' => now()]);
      WorkflowTransitioned::dispatch($instance->fresh(), $log);
      return $instance->fresh();
    });
  }
  public function availableActions(WorkflowInstance $instance, ?Model $actor = null): array
  {
    $d = $this->definitions->get($instance->definition_slug);
    return collect($d['transitions'] ?? [])->where('from', $instance->current_state)->pluck('action')->map(fn($a) => WorkflowAction::from($a))->values()->all();
  }
  private function statusFor(string $to, WorkflowAction $a): WorkflowStatus
  {
    return match ($a) {
      WorkflowAction::Cancel => WorkflowStatus::Cancelled,
      WorkflowAction::Reject => WorkflowStatus::Rejected,
      WorkflowAction::Complete => WorkflowStatus::Completed,
      default => $to === 'completed' ? WorkflowStatus::Completed : WorkflowStatus::Active
    };
  }
}
