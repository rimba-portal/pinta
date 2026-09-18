<?php

declare(strict_types=1);

namespace Rimba\Workflow;

use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Workflow\Console\Commands\SyncWorkflowPermissions;
use Rimba\Workflow\Console\Commands\ValidateWorkflowDefinitions;
use Rimba\Workflow\Contracts\PermissionSynchronizer;
use Rimba\Workflow\Contracts\WorkflowEngine as WorkflowEngineContract;
use Rimba\Workflow\Services\DefinitionRepository;
use Rimba\Workflow\Services\WorkflowEngine;

class WorkflowServiceProvider extends BitesServiceProvider
{
    protected string $viewsPath = __DIR__.'/../resources/views';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->commands([ValidateWorkflowDefinitions::class, SyncWorkflowPermissions::class]);
        }

        $this->publishes([__DIR__.'/../config/pinta.php' => config_path('pinta.php')], 'pinta-config');
        $this->publishes([__DIR__.'/../definitions' => base_path('definitions')], 'pinta-definitions');

    }

    protected function registerPackage(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pinta.php', 'pinta');
        $this->app->singleton(DefinitionRepository::class);
        $this->app->singleton(WorkflowEngineContract::class, WorkflowEngine::class);
        $this->app->alias(WorkflowEngineContract::class, 'pinta');
        $this->app->singleton(PermissionSynchronizer::class, fn ($app) => $app->make(config('pinta.permission_synchronizer')));

    }
}
