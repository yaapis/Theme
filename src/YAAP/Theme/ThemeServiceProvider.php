<?php

declare(strict_types=1);

namespace YAAP\Theme;

use Illuminate\Support\ServiceProvider;

/**
 * Class ThemeServiceProvider.
 */
class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('theme.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // init theme with default finder
        $this->app->singleton('theme', fn ($app) => new Theme($app, $this->app['view']->getFinder(), $this->app['translator']->getLoader()));

        // merge & publish config
        $configPath = __DIR__ . '/../../config/config.php';
        $this->mergeConfigFrom($configPath, 'theme');
        $this->publishes([$configPath => config_path('theme.php')]);

        $this->app->singleton('theme.create', fn ($app) => new Commands\ThemeGeneratorCommand($app['config'], $app['files']));

        $this->app->singleton('theme.destroy', fn ($app) => new Commands\ThemeDestroyCommand($app['config'], $app['files']));

        $this->commands(
            'theme.create',
            'theme.destroy'
        );
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
