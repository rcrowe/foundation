<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Filesystem;
use Illuminate\Foundation\Application;

class FilesystemServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app->share(function() { return new Filesystem; });
	}

}