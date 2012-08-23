<?php namespace Illuminate\Foundation\Managers;

use Illuminate\Foundation\Application;

class DatabaseManager {

	/**
	 * The application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return Illuminate\Database\Connection
	 */
	public function connection($name = null)
	{
		//
	}

}