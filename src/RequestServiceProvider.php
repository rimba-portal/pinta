<?php

declare(strict_types=1);

namespace Rimba\Workflow;

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
    protected string $configName = 'work';

    protected string $viewNamespace = 'bites';

    /*
     * Work currently does not need its own icon set.
     *
     * If Work has resources/svg, change these to:
     *
     * protected string $iconSet = 'work';
     * protected string $iconPrefix = 'work';
     */
    protected string $iconSet = 'bites';

    protected string $iconPrefix = 'bites';

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
