<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Filesystem;
use Illuminate\Blade\Loader;
use Illuminate\Blade\Compiler;
use Illuminate\Blade\Environment;
use Illuminate\Validation\MessageBag;
use Illuminate\Foundation\Application;

class BladeServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$this->registerLoader($app);

		$this->registerEnvironment($app);
	}

	/**
	 * Register the Blade loader with the application.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerLoader($app)
	{
		// First we need to setup the paths for this Blade engine. These are simply the
		// storage path for these templates, as well as the path where the compiled
		// templates are stored for fast rendering. Then we'll create the loader.
		$app['blade.path'] = $app['path'].'/views';

		$app['blade.cache'] = $app['blade.path'].'/cache';

		$app['blade.loader'] = $app->share(function($app)
		{
			return Loader::make($app['blade.path'], $app['blade.cache']);
		});
	}

	/**
	 * Register the Blade environment instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerEnvironment($app)
	{
		$me = $this;

		$app['blade'] = $app->share(function($app) use ($me)
		{
			$blade = new Environment($app['blade.loader']);

			// We will set the application instance as a shared piece of data within the
			// Blade factory so it gets passed to every template that is rendered for
			// convenience. This allows access to the request, input, session, etc.
			$blade->share('app', $app);

			if ($me->hasBoundErrors($app))
			{
				$errors = $app['session']->get('errors');

				$blade->share('errors', $errors);
			}

			// Even if no error object is present in the session, we will still bind an
			// instance onto the template simply so all views can assume that one is
			// available to them. This avoids the need for a lot of pointless ifs.
			else
			{
				$blade->share('errors', new MessageBag);
			}

			return $blade;
		});
	}

	/**
	 * Determine if the given application has errors.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return bool
	 */
	public function hasBoundErrors($app)
	{
		return isset($app['session']) and $app['session']->has('errors');
	}

}