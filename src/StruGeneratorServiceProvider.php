<?php

namespace Stru\LumenGenerator;

use Illuminate\Support\ServiceProvider;
use Stru\LumenGenerator\Commands\Admin\AdminGeneratorCommand;
use Stru\LumenGenerator\Commands\API\APIControllerGeneratorCommand;
use Stru\LumenGenerator\Commands\API\APIGeneratorCommand;
use Stru\LumenGenerator\Commands\Common\ModelGeneratorCommand;
use Stru\LumenGenerator\Commands\RollbackGeneratorCommand;

class StruGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../config/lumen_generator.php';

        $this->publishes([
            $configPath => app()->configPath('stru/lumen_generator.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('api', function ($app) {
            return new APIGeneratorCommand();
        });

        $this->app->singleton('admin', function ($app) {
            return new AdminGeneratorCommand();
        });

        $this->app->singleton('model', function ($app) {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('rollback', function ($app) {
            return new RollbackGeneratorCommand();
        });

        $this->commands([
            'api',
            'admin',
            'model',
            'rollback',
        ]);
    }
}
