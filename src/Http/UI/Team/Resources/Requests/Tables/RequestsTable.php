<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Requests\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class RequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('offering.name')
                    ->label('Offering')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phase_code')
                    ->label('Phase')
                    ->badge(),

                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Requested By'),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
