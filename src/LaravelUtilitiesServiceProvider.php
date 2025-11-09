<?php

namespace Afubasic\LaravelUtilities;

use Illuminate\Support\ServiceProvider;
use Afubasic\LaravelUtilities\Commands\MakeActionCommand;

class LaravelUtilitiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeActionCommand::class,
            ]);
        }
    }
}