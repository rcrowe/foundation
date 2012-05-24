<?php namespace Illuminate\Foundation;

use Silex\ServiceProviderInterface;
use Illuminate\Session\CookieStore;

class BaseServiceProvider implements ServiceProviderInterface {

	/**
	 * Register the service provider.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function register(\Silex\Application $app)
	{
		$services = array('Cookie', 'Events', 'Encrypter', 'Files', 'Session');

		// To register the services we'll simply spin through the array of them and
		// call the registrar function for each service, which will simply return
		// a Closure that we can register with the application's IoC container.
		foreach ($services as $service)
		{
			$resolver = $this->{"register{$service}"}($app);

			$app[strtolower($service)] = $app->share($resolver);
		}
	}

	/**
	 * Register the Illuminate cookie service.
	 *
	 * @param  Silex\Application  $app
	 * @return Closure
	 */
	protected function registerCookie($app)
	{
		$app['cookie.options'] = $this->cookieDefaults();

		// The Illuminate cookie creator is just a convenient way to make cookies
		// that share a given set of options. Typically cookies created by the
		// application will have the same settings so this just DRYs it up.
		return function() use ($app)
		{
			extract($app['cookie.options']);

			return new Cookie($path, $domain, $secure, $httpOnly);
		};
	}

	/**
	 * Get the default cookie options.
	 *
	 * @return array
	 */
	protected function cookieDefaults()
	{
		return array('path' => '/', 'domain' => null, 'secure' => false, 'httpOnly' => true);
	}

	/**
	 * Register the Illuminate events service.
	 *
	 * @param  Silex\Application  $app
	 * @return Closure
	 */
	protected function registerEvents($app)
	{
		return function() use ($app)
		{
			return new \Illuminate\Events\Dispatcher;
		};
	}

	/**
	 * Register the Illuminate encryption service.
	 *
	 * @param  Silex\Application  $app
	 * @return Closure
	 */
	protected function registerEncrypter($app)
	{
		return function() use ($app)
		{
			$key = $app['encrypter.key'];

			return new \Illuminate\Encrypter($key);
		};
	}

	/**
	 * Register the Illuminate filesystem service.
	 *
	 * @param  Silex\Application  $app
	 * @return Closure
	 */
	protected function registerFiles($app)
	{
		return function() use ($app)
		{
			return new \Illuminate\Filesystem;
		};
	}

	/**
	 * Register the Illuminate session service.
	 *
	 * @param  Silex\Application  $app
	 * @return Closure
	 */
	protected function registerSession($app)
	{
		return function() use ($app)
		{
			return new CookieStore($app['encrypter'], $app['cookie']);
		};

		$this->registerSessionEvents($app);
	}

	/**
	 * Register the needed before and after events for sessions.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	protected function registerSessionEvents($app)
	{
		// The session needs to be started and closed, so we will register a
		// before and after event to do just that for us. This will manage
		// loading the session payload, as well as writing the sessions.
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