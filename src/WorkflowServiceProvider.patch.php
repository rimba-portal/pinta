<?php
// Merge these statements into WorkflowServiceProvider.
use Rimba\Workflow\Console\Commands\{SyncWorkflowPermissions,ValidateWorkflowDefinitions};
use Rimba\Workflow\Contracts\PermissionSynchronizer;

// register():
$this->app->singleton(PermissionSynchronizer::class, fn($app) => $app->make(config('pinta.permission_synchronizer')));

// boot():
$this->loadViewsFrom(__DIR__.'/../resources/views', 'pinta');
if ($this->app->runningInConsole()) $this->commands([ValidateWorkflowDefinitions::class, SyncWorkflowPermissions::class]);
