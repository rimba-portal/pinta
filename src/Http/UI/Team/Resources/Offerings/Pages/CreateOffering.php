<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Request\Http\UI\Team\Resources\Offerings\OfferingResource;

class CreateOffering extends CreateRecord
{
    protected static string $resource = OfferingResource::class;
}
