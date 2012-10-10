<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Composer;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {

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
		$app['composer'] = $app->share(function($app)
		{
			return new Composer($app['files'], $app['path.base']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('composer');
	}

}