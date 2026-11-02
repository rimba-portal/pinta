<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class OfferingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),

                Select::make('org_unit_id')
                    ->label('Organizational Unit')
                    ->relationship('orgUnit', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('org_team_id')
                    ->label('Organizational Team')
                    ->relationship('orgTeam', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('form_id')
                    ->label('Request Form')
                    ->relationship('form', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('workflow_id')
                    ->label('Request Workflow')
                    ->relationship('workflow', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('visibility')
                    ->options([
                        'internal' => 'Internal',
                    ])
                    ->default('internal')
                    ->required(),

                Select::make('availability')
                    ->options([
                        'draft' => 'Draft',
                        'available' => 'Available',
                        'suspended' => 'Suspended',
                        'retired' => 'Retired',
                    ])
                    ->default('draft')
                    ->required(),

                TextInput::make('icon')
                    ->maxLength(255),

                TextInput::make('color')
                    ->maxLength(100),

                TextInput::make('sort')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
            ]);
    }
}
