<?php namespace Illuminate\Foundation\Managers;

use Illuminate\Foundation\Application;
use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseManager {

	/**
	 * The application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The database connection factory instance.
	 *
	 * @var Illuminate\Database\Connectors\ConnectionFactory
	 */
	protected $factory;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @param  Illuminate\Database\Connectors\ConnectionFactory  $factory
	 * @return void
	 */
	public function __construct(Application $app, ConnectionFactory $factory)
	{
		$this->app = $app;
		$this->factory = $factory;
	}

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return Illuminate\Database\Connection
	 */
	public function connection($name = null)
	{
		if ( ! isset($this->connections[$name]))
		{
			// If we haven't created this connection, we'll create it based on the config
			// provided in the application. Once we've created the connections we will
			// set the "fetch mode" for PDO which determines the query return types.
			$connection = $this->factory->make($this->getConfig($name));

			$connection->setFetchMode($this->app['config']['database.fetch']);

			$this->connections[$name] = $connection;
		}

		return $this->connections[$name];
	}

	/**
	 * Get the configuration for a connection.
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function getConfig($name)
	{
		$name = $name ?: $this->getDefaultConnection();

		// To get the database connection configuration, we will just pull each of the
		// connection configurations and get the configurations for the given name.
		// If the configuration doesn't exist, we'll throw an exception and bail.
		$connections = $this->app['config']['database.connections'];

		if (is_null($config = array_get($connections, $name)))
		{
			throw new \InvalidArgumentException("Database [$name] not configured.");
		}

		return $config;
	}

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	protected function getDefaultConnection()
	{
		return $this->app['config']['database.default'];
	}

	/**
	 * Dynamically pass methods to the default connection.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}