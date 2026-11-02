<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Requests;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Request\Http\UI\Team\Resources\Requests\Pages\ListRequests;
use Rimba\Request\Http\UI\Team\Resources\Requests\Pages\ViewRequest;
use Rimba\Request\Http\UI\Team\Resources\Requests\Schemas\RequestInfolist;
use Rimba\Request\Http\UI\Team\Resources\Requests\Tables\RequestsTable;
use Rimba\Request\Models\Request;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'number';

    public static function infolist(Schema $schema): Schema
    {
        return RequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRequests::route('/'),
            'view' => ViewRequest::route('/{record}'),
        ];
    }
}
