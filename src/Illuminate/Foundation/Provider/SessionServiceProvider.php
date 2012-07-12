<?php namespace Illuminate\Foundation\Provider;

use Illuminate\Session\CookieStore;
use Silex\ServiceProviderInterface;

class SessionServiceProvider implements ServiceProviderInterface {

	/**
	 * Bootstrap the application events.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function boot(\Silex\Application $app)
	{
		$this->registerSessionEvents($app);
	}

	/**
	 * Register the service provider.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function register(\Silex\Application $app)
	{
		$app['session'] = $app->share(function() use ($app)
		{
			return new CookieStore($app['encrypter'], $app['cookie']);
		});
	}

	/**
	 * Register the events needed for session management.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	protected function registerSessionEvents($app)
	{
		// The session needs to be started and closed, so we will register a
		// before and after event to do all that for us. This will manage
		// loading the session payloads as well as writing the session.
		$app->before(function($request) use ($app)
		{
			$app['session']->start($request);
		});

		$app->after(function($request, $response) use ($app)
		{
			$app['session']->finish($response, $app['cookie']);
		});
	}

}