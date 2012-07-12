<?php namespace Illuminate\Foundation\Provider;

use Illuminate\CookieCreator;
use Silex\ServiceProviderInterface;

class CookieServiceProvider implements ServiceProviderInterface {

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
		$app['cookie.options'] = $this->cookieDefaults();

		// The Illuminate cookie creator is just a convenient way to make cookies
		// that share a given set of options. Typically cookies created by the
		// application will have the same settings so this just DRY's it up.
		$app['cookie'] = $app->share(function() use ($app)
		{
			$options = $app['cookie.options'];

			extract($options);

			return new CookieCreator($path, $domain, $secure, $httpOnly);
		});
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

}