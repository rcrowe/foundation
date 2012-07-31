<?php namespace Illuminate\Foundation\Provider;

use Illuminate\CookieCreator;
use Illuminate\Foundation\Application;

class CookieServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['cookie.options'] = $this->cookieDefaults();

		// The Illuminate cookie creator is just a convenient way to make cookies
		// that share a given set of options. Typically cookies created by the
		// application will have the same settings so this just DRY's it up.
		$app['cookie'] = $app->share(function($app)
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