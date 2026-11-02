<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Requests\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Request\Http\UI\Team\Resources\Requests\RequestResource;

class CreateRequest extends CreateRecord
{
    protected static string $resource = RequestResource::class;
}
