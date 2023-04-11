<?php

declare(strict_types=1);

namespace EmzD\Workflow;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        Route::middleware(['web'])->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'emzd_workflow');
    }

    public function register()
    {
        $this->app->singleton(WorkflowServiceInterface::class, function ($app) {
            return new WorkflowService();
        });
    }
}