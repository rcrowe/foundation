<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;

class FilesServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['files'] = $app->share(function($app)
		{
			return new \Illuminate\Filesystem;
		});
	}

}