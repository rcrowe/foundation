<?php namespace Illuminate\Foundation\Provider;

use Illuminate\Session\CookieStore;
use Illuminate\Foundation\Application;
use Illuminate\Session\TokenMismatchException;

class SessionServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function boot(Application $app)
	{
		$this->registerSessionEvents($app);
	}

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['session'] = $app->share(function($app)
		{
			return new CookieStore($app['encrypter'], $app['cookie']);
		});

		$this->addSessionMiddleware($app);
	}

	/**
	 * Register the events needed for session management.
	 *
	 * @param  Illuminate\Foundation\Application  $app
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

	/**
	 * Register the CSRF middleware for the application.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function addSessionMiddleware($app)
	{
		$app->addMiddleware('csrf', function() use ($app)
		{
			// The "csrf" middleware provides a simple middleware for checking that a
			// CSRF token in the request inputs matches the CSRF token stored for
			// the user in the session data. If it doesn't, we will bail out.
			$token = $app['session']->getToken();

			if ($token !== $app['request']->get('csrf_token'))
			{
				throw new TokenMismatchException;
			}
		});
	}

}