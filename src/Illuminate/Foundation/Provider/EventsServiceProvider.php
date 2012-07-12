<?php namespace Illuminate\Foundation\Provider;

use Silex\ServiceProviderInterface;

class EventsServiceProvider implements ServiceProviderInterface {

	/**
	 * Bootstrap the application events.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function boot(\Silex\Application $app)
	{
		//
	}

	/**
	 * Register the service provider.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function register(\Silex\Application $app)
	{
		$app['events'] = $app->share(function() use ($app)
		{
			return new \Illuminate\Events\Dispatcher;
		});
	}

}