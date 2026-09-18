<?php

namespace Rimba\Workflow;

use Illuminate\Support\ServiceProvider;
use Rimba\Workflow\Contracts\WorkflowEngine as WorkflowEngineContract;
use Rimba\Workflow\Services\DefinitionRepository;
use Rimba\Workflow\Services\WorkflowEngine;

class WorkflowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pinta.php', 'pinta');
        $this->app->singleton(DefinitionRepository::class);
        $this->app->singleton(WorkflowEngineContract::class, WorkflowEngine::class);
        $this->app->alias(WorkflowEngineContract::class, 'pinta');
    }

    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/pinta.php' => config_path('pinta.php')], 'pinta-config');
        $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'pinta-migrations');
        $this->publishes([__DIR__ . '/../definitions' => base_path('definitions')], 'pinta-definitions');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
