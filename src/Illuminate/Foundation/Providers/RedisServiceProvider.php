<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Managers\RedisManager;

class RedisServiceProvider extends ServiceProvider {

	/**
	 * Indicates if the service provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$app['redis'] = $app->share(function($app)
		{
			return new RedisManager($app);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('redis');
	}

}