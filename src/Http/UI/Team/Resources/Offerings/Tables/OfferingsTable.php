<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Rimba\Request\Http\UI\Team\Resources\Offerings\OfferingResource;

class OfferingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('form.name')
                    ->label('Form'),

                Tables\Columns\TextColumn::make('workflow.name')
                    ->label('Workflow'),

                Tables\Columns\TextColumn::make('availability')
                    ->badge(),

                Tables\Columns\TextColumn::make('visibility')
                    ->badge(),
            ])
            ->defaultSort('sort')
            ->recordActions([
                Action::make('use')
                    ->label('Use')
                    ->icon('heroicon-o-play')
                    ->url(
                        fn ($record): string => OfferingResource::getUrl(
                            'use',
                            ['record' => $record],
                        )
                    )
                    ->visible(
                        fn ($record): bool => filled($record->form_id)
                            && $record->availability === 'available'
                    ),

                ViewAction::make(),

                EditAction::make(),
            ]);
    }
}
