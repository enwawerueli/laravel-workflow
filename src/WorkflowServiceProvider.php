<?php

declare(strict_types=1);

namespace EmzD\Workflow;

use Illuminate\Support\ServiceProvider;

/**
 * Class WorkflowServiceProvider
 */
class WorkflowServiceProvider extends ServiceProvider {
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('./emzd_workflow.php')
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(WorkflowServiceInterface::class, function ($app) {
            return new WorkflowService();
        });
    }
}