<?php

declare(strict_types=1);

namespace Rimba\Workflow\Http\UI\Team\Resources\WorkflowDefinitions\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Rimba\Workflow\Enums\WorkflowAction;

final class WorkflowDefinitionSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::components())
            ->columns(1);
    }

    public static function components(): array
    {
        return [
            Tabs::make('Workflow Definition')
                ->tabs([
                    self::generalTab(),
                    self::rolesTab(),
                    self::statesTab(),
                    self::activitiesTab(),
                    self::transitionsTab(),
                    self::startFormTab(),
                    self::dataContractTab(),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ];
    }

    private static function generalTab(): Tab
    {
        return Tab::make('General')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make('Workflow identity')
                    ->description(
                        'Define the workflow identity and runtime template.'
                    )
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->columnSpan(2),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText(
                                'Use lowercase letters, numbers, dots, underscores, or hyphens.'
                            )
                            ->rules([
                                'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
                            ])
                            ->columnSpan(2),

                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        TextInput::make('template')
                            ->default('approval_workflow')
                            ->required(),

                        TextInput::make('version')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Toggle::make('active')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('published')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->columns(4),
            ]);
    }

    private static function rolesTab(): Tab
    {
        return Tab::make('Roles')
            ->icon('heroicon-o-user-group')
            ->schema([
                Section::make('Workflow authorization roles')
                    ->description(
                        'These roles are converted into workflow permissions during synchronization.'
                    )
                    ->schema([
                        self::roleRepeater(
                            'initiator_roles',
                            'Initiator roles'
                        ),

                        self::roleRepeater(
                            'owner_roles',
                            'Owner roles'
                        ),

                        self::roleRepeater(
                            'participant_roles',
                            'Participant roles'
                        ),

                        self::roleRepeater(
                            'approver_roles',
                            'Approver roles'
                        ),

                        TextInput::make('owner_team_slug')
                            ->label('Owner team slug')
                            ->helperText(
                                'Optional team responsible for this workflow.'
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    private static function statesTab(): Tab
    {
        return Tab::make('States')
            ->icon('heroicon-o-map')
            ->schema([
                Section::make('Workflow states')
                    ->description(
                        'The initial state must exist in the states collection.'
                    )
                    ->schema([
                        TextInput::make('initial_state')
                            ->required()
                            ->default('draft')
                            ->columnSpanFull(),

                        KeyValue::make('states')
                            ->keyLabel('State')
                            ->valueLabel('Label')
                            ->addActionLabel('Add state')
                            ->reorderable()
                            ->required()
                            ->default([
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'completed' => 'Completed',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function activitiesTab(): Tab
    {
        return Tab::make('Activities')
            ->icon('heroicon-o-queue-list')
            ->schema([
                Repeater::make('activities')
                    ->label('Workflow activities')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('state')
                            ->required()
                            ->helperText(
                                'State in which this activity becomes relevant.'
                            ),

                        TextInput::make('assignee_role')
                            ->label('Assignee role')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Add activity')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(
                        fn (array $state): string => $state['title']
                            ?? $state['slug']
                            ?? 'New activity'
                    )
                    ->columnSpanFull(),
            ]);
    }

    private static function transitionsTab(): Tab
    {
        return Tab::make('Transitions')
            ->icon('heroicon-o-arrows-right-left')
            ->schema([
                Repeater::make('transitions')
                    ->label('State transitions')
                    ->schema([
                        TextInput::make('from')
                            ->label('From state')
                            ->required(),

                        Select::make('action')
                            ->options(self::workflowActionOptions())
                            ->required()
                            ->searchable(),

                        TextInput::make('to')
                            ->label('To state')
                            ->required(),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->addActionLabel('Add transition')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(
                        function (array $state): string {
                            $from = $state['from'] ?? '?';
                            $action = $state['action'] ?? '?';
                            $to = $state['to'] ?? '?';

                            return "{$from} → {$action} → {$to}";
                        }
                    )
                    ->columnSpanFull(),
            ]);
    }

    private static function startFormTab(): Tab
    {
        return Tab::make('Start Form')
            ->icon('heroicon-o-document-text')
            ->schema([
                Repeater::make('start_form')
                    ->label('Launch form fields')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'select' => 'Select',
                                'date' => 'Date',
                                'boolean' => 'Boolean',
                            ])
                            ->default('text')
                            ->required(),

                        Toggle::make('required')
                            ->default(false)
                            ->inline(false),

                        KeyValue::make('options')
                            ->label('Select options')
                            ->keyLabel('Value')
                            ->valueLabel('Label')
                            ->visible(
                                fn (callable $get): bool => $get('type') === 'select'
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->defaultItems(0)
                    ->addActionLabel('Add form field')
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(
                        fn (array $state): string => $state['label']
                            ?? $state['name']
                            ?? 'New field'
                    )
                    ->columnSpanFull(),
            ]);
    }

    private static function dataContractTab(): Tab
    {
        return Tab::make('Inputs & Outputs')
            ->icon('heroicon-o-arrow-path-rounded-square')
            ->schema([
                Section::make('Workflow data contract')
                    ->schema([
                        KeyValue::make('inputs')
                            ->keyLabel('Input')
                            ->valueLabel('Description')
                            ->addActionLabel('Add input'),

                        KeyValue::make('outputs')
                            ->keyLabel('Output')
                            ->valueLabel('Description')
                            ->addActionLabel('Add output'),
                    ])
                    ->columns(2),
            ]);
    }

    private static function roleRepeater(
        string $name,
        string $label,
    ): Repeater {
        return Repeater::make($name)
            ->label($label)
            ->simple(
                TextInput::make('role')
                    ->required()
                    ->maxLength(255)
            )
            ->defaultItems(0)
            ->addActionLabel('Add role');
    }

    private static function workflowActionOptions(): array
    {
        $options = [];

        foreach (WorkflowAction::cases() as $action) {
            $options[$action->value] = $action->label();
        }

        return $options;
    }
}
