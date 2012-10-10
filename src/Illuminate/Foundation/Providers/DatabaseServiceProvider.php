<?php namespace Illuminate\Foundation\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Managers\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseServiceProvider extends ServiceProvider {

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
		$connections = array_keys($app['config']['database.connections']);

		// To setup the Eloquent ORM, we will register a resolver for each connection
		// that is configured for the application. We'll defer the creation of any
		// of the connections using a Closure which gets resolved when executed.
		foreach ($connections as $name)
		{
			Model::addConnection($name, function() use ($app, $name)
			{
				return $app['db']->connection($name);
			});
		}

		Model::setDefaultConnectionName($app['config']['database.default']);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function getProvidedServices()
	{
		return array('db', 'db.factory');
	}

}