<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Managers\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register(Application $app)
	{
		$app['db.factory'] = $app->share(function()
		{
			return new ConnectionFactory;
		});

		$app['db'] = $app->share(function($app)
		{
			return new DatabaseManager($app, $app['db.factory']);
		});
	}

}