<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OfferingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code'),

                TextEntry::make('name'),

                TextEntry::make('description'),

                TextEntry::make('orgUnit.name')
                    ->label('Organizational Unit'),

                TextEntry::make('orgTeam.name')
                    ->label('Organizational Team'),

                TextEntry::make('form.name')
                    ->label('Request Form'),

                TextEntry::make('workflow.name')
                    ->label('Request Workflow'),

                TextEntry::make('visibility'),

                TextEntry::make('availability'),
            ]);
    }
}
