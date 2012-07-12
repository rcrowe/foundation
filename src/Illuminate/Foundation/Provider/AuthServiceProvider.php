<?php namespace Illuminate\Foundation\Provider;

use Illuminate\Auth\Guard;
use Silex\ServiceProviderInterface;

class AuthServiceProvider implements ServiceProviderInterface {

	/**
	 * Bootstrap the application events.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function boot(\Silex\Application $app)
	{
		$this->registerAuthEvents($app);
	}

	/**
	 * Register the service provider.
	 *
	 * @param  Silex\Application  $app
	 * @return void
	 */
	public function register(\Silex\Application $app)
	{
		$app['auth'] = $app->share(function() use ($app)
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
			// secure, encrypted cookie values to get generated for those cookies.
			$guard->setEncrypter($app['encrypter']);

			return $guard;
		});
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
			// they are attached onto Response objects at the end of the requests.
			if (isset($app['auth.loaded']))
			{
				foreach ($app['auth']->getQueuedCookies() as $cookie)
				{
					$response->headers->setCookie($cookie);
				}
			}
		});
	}

}