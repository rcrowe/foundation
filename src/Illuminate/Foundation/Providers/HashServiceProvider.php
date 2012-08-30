<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Hashing\BcryptHasher;
use Illuminate\Foundation\Application;

class HashServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['hash'] = $app->share(function() { return new BcryptHasher; });
	}

}