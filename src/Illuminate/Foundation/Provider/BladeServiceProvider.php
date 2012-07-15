<?php namespace Illuminate\Foundation\Provider;

use Illuminate\Filesystem;
use Illuminate\Blade\Loader;
use Illuminate\Blade\Factory;
use Illuminate\Blade\Compiler;
use Silex\ServiceProviderInterface;

class BladeServiceProvider implements ServiceProviderInterface {

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
		$app['blade.loader'] = $app->share(function($app)
		{
			// We'll create a Blade loader instance with the path and cache paths set on
			// the application. The loader is responsible for actually returning the
			// fully qualified paths to the blade views to the factory instances.
			$path = $app['blade.path'];

			$cache = $app['blade.cache'];

			$loader = new Loader(new Compiler, new Filesystem, $path, $cache);

			return $loader;
		});

		$app['blade'] = $app->share(function($app)
		{
			$blade = new Factory($app['blade.loader']);

			// We will set the application instance as a shared piece of data within the
			// Blade factory so it gets passed to every template that is rendered for
			// convenience. This allows access to the request, input, session, etc.
			$blade->share('app', $app);

			if (isset($app['session']) and $app['session']->has('errors'))
			{
				$blade->share('errors', $app['session']->get('errors'));
			}

			return $blade;
		});
	}

}