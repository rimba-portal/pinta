<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Requests\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('number'),

                TextEntry::make('offering.name')
                    ->label('Offering'),

                TextEntry::make('phase_code')
                    ->label('Phase')
                    ->badge(),

                TextEntry::make('requester.name')
                    ->label('Requested By'),

                TextEntry::make('submitted_at')
                    ->dateTime(),

                KeyValueEntry::make('formSubmission.data')
                    ->label('Submitted Data'),
            ]);
    }
}
