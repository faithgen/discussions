<?php

namespace Faithgen\Discussions;

use FaithGen\SDK\Traits\ConfigTrait;
use Illuminate\Support\ServiceProvider;

class DiscussionsServiceProvider extends ServiceProvider
{
    use ConfigTrait;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->setUpSourceFiles(function (){
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('discussions.php'),
            ], 'faithgen-discussions-config');
        });

        if ($this->app->runningInConsole()) {


            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/discussions'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/discussions'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/discussions'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'faithgen-discussions');

        // Register the main class to use with the facade
        $this->app->singleton('discussions', function () {
            return new Discussions;
        });
    }

    public function routeConfiguration(): array
    {
        return [
            'prefix'     => config('faithgen-discussions.prefix'),
            'middleware' => config('faithgen-discussions.middlewares'),
        ];
    }
}
