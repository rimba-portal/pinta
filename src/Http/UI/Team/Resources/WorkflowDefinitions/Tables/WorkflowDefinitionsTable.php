<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Schemas\WorkflowDefinitionSchema;
use Rimba\Workflow\Models\WorkflowDefinition;
use Rimba\Workflow\Services\WorkflowDefinitionRepository;

final class WorkflowDefinitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Workflow')
                    ->description(
                        fn (WorkflowDefinition $record): string => (string) $record->slug
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('template')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => str($state ?? 'workflow')
                            ->replace('_', ' ')
                            ->headline()
                            ->toString()
                    )
                    ->color('info'),

                TextColumn::make('version')
                    ->label('Version')
                    ->prefix('v')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('states')
                    ->label('States')
                    ->state(
                        fn (WorkflowDefinition $record): int => count($record->states ?? [])
                    )
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('activities')
                    ->label('Activities')
                    ->state(
                        fn (WorkflowDefinition $record): int => count($record->activities ?? [])
                    )
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('transitions')
                    ->label('Transitions')
                    ->state(
                        fn (WorkflowDefinition $record): int => count($record->transitions ?? [])
                    )
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                IconColumn::make('active')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('published')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label('Active'),

                TernaryFilter::make('published')
                    ->label('Published'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->fillForm(
                        fn (WorkflowDefinition $record): array => $record->toDefinitionArray()
                    )
                    ->schema(
                        WorkflowDefinitionSchema::components()
                    )
                    ->modalHeading(
                        fn (WorkflowDefinition $record): string => "Edit {$record->title}"
                    )
                    ->modalWidth('7xl')
                    ->action(
                        function (
                            WorkflowDefinition $record,
                            array $data,
                            $livewire,
                        ): void {
                            $record->fill($data);

                            app(WorkflowDefinitionRepository::class)
                                ->save($record);

                            $livewire->resetTable();

                            Notification::make()
                                ->title('Workflow updated')
                                ->body(
                                    "Workflow [{$data['slug']}] was saved."
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('validate')
                    ->label('Validate')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->action(
                        function (
                            WorkflowDefinition $record,
                        ): void {
                            $errors = app(
                                WorkflowDefinitionRepository::class
                            )->validate($record);

                            if ($errors === []) {
                                Notification::make()
                                    ->title('Workflow is valid')
                                    ->body(
                                        "No validation errors were found in [{$record->slug}]."
                                    )
                                    ->success()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Workflow validation failed')
                                ->body(implode(PHP_EOL, $errors))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    ),

                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->schema([
                        TextInput::make(
                            'slug'
                        )
                            ->label('New workflow slug')
                            ->required()
                            ->rules([
                                'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
                            ]),

                        TextInput::make(
                            'title'
                        )
                            ->label('New workflow title')
                            ->required(),
                    ])
                    ->action(
                        function (
                            WorkflowDefinition $record,
                            array $data,
                            $livewire,
                        ): void {
                            app(WorkflowDefinitionRepository::class)
                                ->duplicate(
                                    $record,
                                    $data['slug'],
                                    $data['title'],
                                );

                            $livewire->resetTable();

                            Notification::make()
                                ->title('Workflow duplicated')
                                ->body(
                                    "Workflow [{$data['slug']}] was created."
                                )
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('togglePublished')
                    ->label(
                        fn (WorkflowDefinition $record): string => $record->published
                                ? 'Unpublish'
                                : 'Publish'
                    )
                    ->icon(
                        fn (WorkflowDefinition $record): string => $record->published
                                ? 'heroicon-o-eye-slash'
                                : 'heroicon-o-rocket-launch'
                    )
                    ->color(
                        fn (WorkflowDefinition $record): string => $record->published
                                ? 'warning'
                                : 'success'
                    )
                    ->requiresConfirmation()
                    ->action(
                        function (
                            WorkflowDefinition $record,
                            $livewire,
                        ): void {
                            $record->published =
                                ! (bool) $record->published;

                            app(WorkflowDefinitionRepository::class)
                                ->save($record);

                            $livewire->resetTable();

                            Notification::make()
                                ->title(
                                    $record->published
                                        ? 'Workflow published'
                                        : 'Workflow unpublished'
                                )
                                ->success()
                                ->send();
                        }
                    ),

                DeleteAction::make()
                    ->label('Delete')
                    ->modalHeading('Delete workflow definition')
                    ->modalDescription(
                        fn (WorkflowDefinition $record): string => "Delete workflow [{$record->slug}]? "
                            .'This removes the JSON definition file.'
                    )
                    ->using(
                        function (
                            WorkflowDefinition $record,
                        ): bool {
                            app(WorkflowDefinitionRepository::class)
                                ->delete($record);

                            return true;
                        }
                    )
                    ->successNotificationTitle(
                        'Workflow definition deleted'
                    ),
            ])
            ->recordAction('edit')
            ->recordUrl(null)
            ->defaultSort('title')
            ->striped()
            ->emptyStateHeading('No workflow definitions')
            ->emptyStateDescription(
                'Create the first JSON-backed workflow definition.'
            )
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
