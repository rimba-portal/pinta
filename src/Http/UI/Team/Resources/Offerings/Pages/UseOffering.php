<?php

declare(strict_types=1);

namespace Rimba\Request\Http\UI\Team\Resources\Offerings\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Rimba\Request\Http\UI\Team\Resources\Offerings\OfferingResource;

class UseOffering extends Page
{
    use InteractsWithRecord;

    protected static string $resource = OfferingResource::class;

    protected string $view =
        'rimba-request::filament.offerings.pages.use-offering';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(
            $this->record->form_id,
            404,
            'This offering does not have a request form.',
        );
    }

    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }
}
