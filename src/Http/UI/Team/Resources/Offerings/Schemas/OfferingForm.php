<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OfferingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),

                TextInput::make('name')
                    ->required(),

                Textarea::make('description')
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

                TextInput::make('icon'),

                TextInput::make('color'),

                TextInput::make('sort')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Textarea::make('settings')
                    ->columnSpanFull(),
            ]);
    }
}
