<?php

namespace Rimba\Workflow\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Rimba\Workflow\Contracts\WorkflowEngine;
use Rimba\Workflow\Enums\WorkflowAction;

class Buttons
{
  public static function make(WorkflowAction|string $workflowAction): Action
  {
    $workflowAction = $workflowAction instanceof WorkflowAction ? $workflowAction : WorkflowAction::from($workflowAction);
    return Action::make($workflowAction->value)
      ->label($workflowAction->label())
      ->icon($workflowAction->icon())
      ->color($workflowAction->color())
      ->requiresConfirmation()
      ->schema($workflowAction === WorkflowAction::Assign ? [] : [Textarea::make('comment')
        ->maxLength(2000)])
      ->visible(fn($record) => in_array($workflowAction, app(WorkflowEngine::class)
        ->availableActions($record)))
      ->action(fn($record, array $data) => app(WorkflowEngine::class)
        ->perform($record, $workflowAction, auth()->user(), $data));
  }
  public static function all(): array
  {
    return array_map(fn($a) => self::make($a), WorkflowAction::cases());
  }
}
