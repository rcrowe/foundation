<?php namespace Illuminate\Foundation;

use Illuminate\Auth\Guard;
use Illuminate\CookieCreator;
use Silex\ServiceProviderInterface;
use Illuminate\Session\CookieStore;

class BaseServiceProvider implements ServiceProviderInterface {

	/**
	 * Bootstraps the application.
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
		$services = array('Auth', 'Cookie', 'Events', 'Encrypter', 'Files', 'Session');

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
	 * Register the Illuminate authentication service.
	 *
	 * @param  Silex\Application  $app
	 * @return Closure
	 */
	protected function registerAuth($app)
	{
		$this->registerAuthEvents($app);

		return function() use ($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			if ( ! isset($app['auth.provider']))
			{
				throw new \RuntimeException("Auth service requires provider.");
			}

			// The user provider is responsible for actually fetching the user information
			// out of whatever storage mechanism the developer is using and giving the
			// user back to the guard. This interface abstracts the user retrieval.
			$provider = $app['auth.provider'];

			$guard = new Guard($provider, $app['session'], $app['request']);

			$guard->setCookieCreator($app['cookie']);

			// When using the remember me functionality of the authentication services we
			// will need to be set the encryption isntance of the guard, which allows
			// secure, encrypted cookie values to be generated for those cookies.
			$guard->setEncrypter($app['encrypter']);

			return $guard;
		};
	}

	/**
	 * Register the events needed for authentication.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	protected function registerAuthEvents($app)
	{
		$app->after(function($request, $response) use ($app)
		{
			// If the authentication service has been used, we'll check for any cookies
			// that may be queued by the service. These cookies are all queued until
			// they are attached to a Response object at the end of the requests.
			if ($app['auth.loaded'])
			{
				foreach ($app['auth']->getQueuedCookies() as $cookie)
				{
					$response->headers->setCookie($cookie);
				}
			}
		});
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
			$options = $app['cookie.options'];

			extract($options);

			return new CookieCreator($path, $domain, $secure, $httpOnly);
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
		$this->registerSessionEvents($app);

		return function() use ($app)
		{
			return new CookieStore($app['encrypter'], $app['cookie']);
		};
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