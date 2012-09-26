<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AliasServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$app['alias'] = AliasLoader::getInstance($app['config']['app.aliases']);

		$app['alias']->register();
	}

}