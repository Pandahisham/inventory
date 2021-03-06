<?php

namespace Stevebauman\Inventory;

use Illuminate\Support\ServiceProvider;

/**
 * Class InventoryServiceProvider
 * @package Stevebauman\Inventory
 */
class InventoryServiceProvider extends ServiceProvider
{
    /**
     * Inventory version
     *
     * @var string
     */
    const VERSION = '1.3.0';

	/**
	 * Stores the package configuration separator
	 * for Laravel 5 compatibility
	 *
	 * @var string
	 */
	public static $packageConfigSeparator = '::';

	/**
	 * The laravel version number. This is used for the install commands
	 *
	 * @var int
	 */
	public static $laravelVersion = 4;

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
        /*
         * If the package method exists, we're using Laravel 4, if not, we're on 5
         */
		if(method_exists($this, 'package'))
        {
			$this->package('stevebauman/inventory');
		} else
        {
			$this::$packageConfigSeparator = '.';

			$this::$laravelVersion = 5;

			$this->loadTranslationsFrom(__DIR__ . '/../../lang', 'inventory');

			$this->publishes([
				__DIR__ . '/../../config/config.php' => config_path('inventory.php'),
			], 'config');

			$this->publishes([
				__DIR__ . '/../../migrations/' => base_path('/database/migrations'),
			], 'migrations');
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        /*
         * Bind the install command
         */
		$this->app->bind('inventory:install', function() {
			return new Commands\InstallCommand();
		});

        /*
         * Bind the check-schema command
         */
		$this->app->bind('inventory:check-schema', function() {
			return new Commands\SchemaCheckCommand();
		});

        /*
         * Bind the run migrations command
         */
		$this->app->bind('inventory:run-migrations', function() {
			return new Commands\RunMigrationsCommand();
		});

        /*
         * Register the commands
         */
		$this->commands(array(
			'inventory:install',
			'inventory:check-schema',
			'inventory:run-migrations',
		));

        /*
         * Include the helpers file
         */
		include __DIR__ .'/../../helpers.php';
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('inventory');
	}

}
