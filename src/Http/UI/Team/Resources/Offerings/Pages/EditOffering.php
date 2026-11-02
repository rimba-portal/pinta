<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Rimba\Request\Http\UI\Team\Resources\Offerings\OfferingResource;

class EditOffering extends EditRecord
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
