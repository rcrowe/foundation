<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;

class EventsServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['events'] = $app->share(function($app)
		{
			return new \Illuminate\Events\Dispatcher;
		});
	}

}