<?php namespace Illuminate\Foundation\Providers;

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
			return new \Illuminate\Encrypter($app['config']['app.key']);
		});
	}

}