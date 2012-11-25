<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AliasServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['alias'] = AliasLoader::getInstance($this->app['config']['app.aliases']);

		$this->app['alias']->register();
	}

}