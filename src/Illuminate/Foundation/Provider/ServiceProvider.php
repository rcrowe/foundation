<?php namespace Illuminate\Foundation\Provider;

use Illuminate\Foundation\Application;

abstract class ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function boot(Application $app) {}

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	abstract public function register(Application $app);

}