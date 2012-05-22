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
		$services = array('Events', 'Encrypter', 'Files', 'Session');

		// To register the services we'll simply spin through the array of them and
		// call the registrar function for each service, which will just return
		// a Closure that we can register with the application IoC container.
		foreach ($services as $service)
		{
			$resolver = $this->{"register{$service}"}($app);

			$app[strtolower($service)] = $app->share($resolver);
		}
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
	}

}