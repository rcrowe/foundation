<?php namespace Illuminate\Foundation\Provider;

use Illuminate\Foundation\Application;

class EncrypterServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['encrypter'] = $app->share(function($app)
		{
			return new \Illuminate\Encrypter($app['encrypter.key']);
		});
	}

}