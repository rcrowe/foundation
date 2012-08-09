<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Managers\CacheManager;

class CacheServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['cache'] = $app->share(function($app)
		{
			return new CacheManager($app);
		});
	}

}