<?php namespace Illuminate\Foundation\Testing;

use Illuminate\Auth\UserInterface;

class TestCase extends \PHPUnit_Framework_TestCase {

	/**
	 * The Illuminate application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The HttpKernel client instance.
	 *
	 * @var Illuminate\Foundation\Testing\CLient
	 */
	protected $client;

	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->refreshApplication();
	}

	/**
	 * Refresh the application instance.
	 *
	 * @return void
	 */
	protected function refreshApplication()
	{
		$this->app = $this->createApplication();

		$this->client = $this->createClient();
	}

	/**
	 * Call the given URI and return the Response.
	 *
	 * @param  dynamic
	 * @return Illuminate\Http\Response
	 */
	public function call()
	{
		call_user_func_array(array($this->client, 'request'), func_get_args());

		return $this->client->getResponse();
	}

	/**
	 * Set the currently logged in user for the application.
	 *
	 * @param  Illuminate\Auth\UserInterface  $user
	 * @param  string  $driver
	 * @return void
	 */
	public function be(UserInterface $user, $driver = null)
	{
		$this->app['auth']->driver($driver)->setUser($user);
	}

	/**
	 * Seed a given database connection.
	 *
	 * @param  string  $connection
	 * @return void
	 */
	public function seed($connection = null)
	{
		$connection = $this->app['db']->connection($connection);

		$this->app['seeder']->seed($connection, $this->app['path'].'/database/seeds');
	}

	/**
	 * Create a new HttpKernel client instance.
	 *
	 * @param  array  $server
	 * @return Symfony\Component\HttpKernel\Client
	 */
	protected function createClient(array $server = array())
	{
		return new Client($this->app, $server);
	}

}