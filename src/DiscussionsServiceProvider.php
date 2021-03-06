<?php

namespace Faithgen\Discussions;

use Faithgen\Discussions\Models\Discussion;
use Faithgen\Discussions\Observers\DiscussionObserver;
use Faithgen\Discussions\Services\DiscussionService;
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
        $this->registerRoutes(__DIR__.'/../routes/discussions.php', __DIR__.'/../routes/source.php');

        $this->setUpSourceFiles(function () {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('discussions.php'),
            ], 'faithgen-discussions-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'faithgen-discussions-migrations');

            $this->loadFactoriesFrom(__DIR__.'/../database/factories');
        });

        $this->publishes([
            __DIR__.'/../storage' => storage_path('app/public'),
        ], 'faithgen-discussions-storage');

        Discussion::observe(DiscussionObserver::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'faithgen-discussions');

        $this->app->singleton(DiscussionService::class);

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
