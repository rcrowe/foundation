<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;

class EncrypterServiceProvider extends ServiceProvider {

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
		$app['encrypter'] = $app->share(function($app)
		{
			return new \Illuminate\Encrypter($app['config']['app.key']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('encrypter');
	}

}