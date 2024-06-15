<?php

namespace Manta\MantaCms;

use Illuminate\Support\ServiceProvider;

class MantaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register any package services.
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\CopyMantaCommand::class,
            ]);
        }
    }
}
