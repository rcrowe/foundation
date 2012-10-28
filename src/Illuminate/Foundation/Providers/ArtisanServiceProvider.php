<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Artisan;
use Illuminate\Support\ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$app['artisan'] = $app->share(function($app)
		{
			return new Artisan($app);
		});
	}

}