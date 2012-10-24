<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Controllers\FilterParser;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

class ControllerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if the service provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$this->registerReader($app);

		// Controller may use annotations to specify filters, which uses the Doctrine
		// annotations component to parse those annotations out then apply them to
		// the route being executed, so we need to register the parser instance.
		$this->registerParser($app);

		$this->requireAnnotations();

		$this->registerGenerator($app);
	}

	/**
	 * Register the filter parser instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerParser($app)
	{
		$app['filter.parser'] = $app->share(function($app)
		{
			$path = $app['path'].'/storage/meta';

			return new FilterParser($app['annotation.reader'], $app['files'], $path);
		});
	}

	/**
	 * Register the annotation reader.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerReader($app)
	{
		$app['annotation.reader'] = $app->share(function()
		{
			$reader = new SimpleAnnotationReader;

			$reader->addNamespace('Illuminate\Routing\Controllers');

			return $reader;
		});
	}

	/**
	 * Manually require the controller annotation definitions.
	 *
	 * @return void
	 */
	protected function requireAnnotations()
	{
		spl_autoload_call('Illuminate\Routing\Controllers\Before');

		spl_autoload_call('Illuminate\Routing\Controllers\After');
	}

	/**
	 * Register the controller generator command.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerGenerator($app)
	{
		$app['command.controller.make'] = $app->share(function($app)
		{
			// The controller generator is responsible for building resourceful controllers
			// quickly and easily for the developers via the Artisan CLI. We'll go ahead
			// and register this command instances in this container for registration.
			$path = $app['path'].'/controllers';

			$generator = new ControllerGenerator($app['files'], $path);

			return new MakeControllerCommand($generator);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('filter.parser');
	}

}