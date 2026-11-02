<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Pages\CreateOffering;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Pages\EditOffering;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Pages\ListOfferings;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Pages\UseOffering;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Pages\ViewOffering;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Schemas\OfferingForm;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Schemas\OfferingInfolist;
use Rimba\Request\Http\UI\Team\Resources\Offerings\Tables\OfferingsTable;
use Rimba\Request\Models\Offering;

class OfferingResource extends Resource
{
    protected static ?string $model = Offering::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OfferingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OfferingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfferingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfferings::route('/'),
            'create' => CreateOffering::route('/create'),

            /*
             * Keep custom /use before generic /{record}.
             */
            'use' => UseOffering::route('/{record}/use'),

            'view' => ViewOffering::route('/{record}'),
            'edit' => EditOffering::route('/{record}/edit'),
        ];
    }
}
