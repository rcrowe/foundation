<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider {

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

}