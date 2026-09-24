<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Schemas\WorkflowDefinitionSchema;
use Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\WorkflowDefinitionResource;
use Rimba\Workflow\Models\WorkflowDefinition;
use Rimba\Workflow\Services\WorkflowDefinitionRepository;

final class ManageWorkflowDefinitions extends ListRecords
{
    protected static string $resource =
        WorkflowDefinitionResource::class;

    protected static ?string $title = 'Workflow Studio';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createWorkflow')
                ->label('New Workflow')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->schema(
                    WorkflowDefinitionSchema::components()
                )
                ->fillForm([
                    'template' => 'approval_workflow',
                    'version' => 1,
                    'active' => true,
                    'published' => false,
                    'initial_state' => 'draft',
                    'states' => [
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'completed' => 'Completed',
                    ],
                    'activities' => [],
                    'transitions' => [],
                    'start_form' => [],
                    'inputs' => [],
                    'outputs' => [],
                    'initiator_roles' => [],
                    'owner_roles' => [],
                    'participant_roles' => [],
                    'approver_roles' => [],
                ])
                ->modalHeading('Create Workflow Definition')
                ->modalDescription(
                    'Create a JSON-backed workflow definition.'
                )
                ->modalSubmitActionLabel('Create Workflow')
                ->modalWidth('7xl')
                ->action(
                    function (array $data): void {
                        $workflowDefinitionRepository = app(
                            WorkflowDefinitionRepository::class
                        );

                        $workflowDefinitionRepository->create($data);

                        $this->resetTable();

                        Notification::make()
                            ->title('Workflow created')
                            ->body(
                                "Workflow [{$data['slug']}] was created."
                            )
                            ->success()
                            ->send();
                    }
                ),

            Action::make('validateAll')
                ->label('Validate All')
                ->icon('heroicon-o-check-badge')
                ->color('gray')
                ->action(function (): void {
                    $workflowDefinitionRepository = app(
                        WorkflowDefinitionRepository::class
                    );

                    $invalid = $workflowDefinitionRepository
                        ->all()
                        ->mapWithKeys(
                            function (
                                WorkflowDefinition $workflow,
                            ) use ($workflowDefinitionRepository): array {
                                $errors = $workflowDefinitionRepository->validate(
                                    $workflow
                                );

                                return $errors === []
                                    ? []
                                    : [$workflow->slug => $errors];
                            }
                        );

                    if ($invalid->isEmpty()) {
                        Notification::make()
                            ->title('All workflows are valid')
                            ->body(
                                'No definition validation errors were found.'
                            )
                            ->success()
                            ->send();

                        return;
                    }

                    $message = $invalid
                        ->map(
                            fn (
                                array $errors,
                                string $slug,
                            ): string => $slug.': '.implode('; ', $errors)
                        )
                        ->implode(PHP_EOL);

                    Notification::make()
                        ->title('Validation errors found')
                        ->body($message)
                        ->danger()
                        ->persistent()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->records(
                fn (): Collection => app(WorkflowDefinitionRepository::class)
                    ->all()
            );
    }
}
