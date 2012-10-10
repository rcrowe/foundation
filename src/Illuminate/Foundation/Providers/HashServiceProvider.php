<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider {

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
		$app['hash'] = $app->share(function() { return new BcryptHasher; });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('hash');
	}

}