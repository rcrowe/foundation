<?php namespace Illuminate\Foundation\Providers;

use Illuminate\View\PhpEngine;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Managers\ViewManager;

class ViewServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['view'] = $app->share(function($app)
		{
			return new ViewManager($app);
		});
	}

}