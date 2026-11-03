<?php

declare(strict_types=1);

namespace Rimba\Request;

use Illuminate\Support\Facades\Event;
use Packstub\FormBuilder\Events\SubmissionReceived;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Request\Listeners\CreateRequestFromFormSubmission;

class RequestServiceProvider extends BitesServiceProvider
{
    /*
     * Keep these shared if your intention is that all RIMBA packages
     * contribute into the same Bites namespace/configuration.
     */
    // protected string $configName = 'work';

    protected string $viewsPath = __DIR__.'/../resources/views';

    protected string $iconsPath = __DIR__.'/../resources/svg';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Event::listen(
            SubmissionReceived::class,
            CreateRequestFromFormSubmission::class,
        );
    }

    protected function registerPackage(): void
    {
        //
    }
}
