<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\AliasLoader;

class AliasServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['alias'] = AliasLoader::getInstance($app['config']['app.aliases']);

		$app['alias']->register();
	}

}