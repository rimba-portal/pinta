<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Requests\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Request\Http\UI\Team\Resources\Requests\RequestResource;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
