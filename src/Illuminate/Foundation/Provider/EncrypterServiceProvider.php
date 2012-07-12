<?php namespace Illuminate\Foundation\Provider;

use Silex\ServiceProviderInterface;

class EncrypterServiceProvider implements ServiceProviderInterface {

	/**
	 * Bootstrap the application events.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function boot(\Silex\Application $app)
	{
		//
	}

	/**
	 * Register the service provider.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function register(\Silex\Application $app)
	{
		$app['encrypter'] = $app->share(function() use ($app)
		{
			return new \Illuminate\Encrypter($app['encrypter.key']);
		});
	}

}