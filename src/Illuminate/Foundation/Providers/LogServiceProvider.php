<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Log\Writer;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

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

}