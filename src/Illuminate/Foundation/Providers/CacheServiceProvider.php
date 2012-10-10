<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\MemcachedConnector;
use Illuminate\Foundation\Managers\CacheManager;

class CacheServiceProvider extends ServiceProvider {

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
		$app['cache'] = $app->share(function($app)
		{
			return new CacheManager($app);
		});

		$app['memcached.connector'] = $app->share(function()
		{
			return new MemcachedConnector;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('cache', 'memcached.connector');
	}

}