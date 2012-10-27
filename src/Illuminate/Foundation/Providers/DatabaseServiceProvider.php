<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Managers\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$this->registerEloquent($app);

		// The connection factory is used to create the actual connection instances on
		// the database. We will inject the factory into the manager so that it may
		// make the connections while they are actually needed and not of before.
		$app['db.factory'] = $app->share(function()
		{
			return new ConnectionFactory;
		});

		$app['db'] = $app->share(function($app)
		{
			return new DatabaseManager($app, $app['db.factory']);
		});
	}

	/**
	 * Register the database connections with the Eloquent ORM.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerEloquent($app)
	{
		Model::setConnectionResolver($app['db']);
	}

}