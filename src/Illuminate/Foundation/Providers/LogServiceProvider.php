<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Log\Writer;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

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
		$app['log'] = $app->share(function($app)
		{
			return new Writer(new \Monolog\Logger('log'));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('log');
	}

}