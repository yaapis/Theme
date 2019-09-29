<?php namespace YAAP\Theme;

use Illuminate\Support\ServiceProvider;

/**
 * Class ThemeServiceProvider
 * @package YAAP\Theme
 */
class ThemeServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('theme.php'),
        ], 'config');
    }

    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

        // init theme with default finder
        $this->app->singleton('theme', function($app) {
            $theme = new Theme($app, $this->app['view']->getFinder(), $this->app['translator']->getLoader());
            return $theme;
        });


        // merge & publish config
        $configPath = __DIR__ . '/../../config/config.php';
        $this->mergeConfigFrom($configPath, 'theme');
        $this->publishes([$configPath => config_path('theme.php')]);


        $this->app->singleton('theme.create',function ($app) {
            return new Commands\ThemeGeneratorCommand($app['config'], $app['files']);
        });

        $this->app->singleton('theme.destroy',function ($app) {
            return new Commands\ThemeDestroyCommand($app['config'], $app['files']);
        });

        $this->commands(
            'theme.create',
            'theme.destroy'
        );


	}



	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
