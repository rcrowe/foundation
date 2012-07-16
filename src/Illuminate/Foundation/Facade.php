<?php namespace Illuminate\Foundation;

abstract class Facade {

	/**
	 * The application instance being facaded.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	protected static $app;

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	abstract public static function getName();

	/**
	 * Set the application instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function setFacadeApplication(Application $app)
	{
		static::$app = $app;
	}

	/**
	 * Load the file of facade definitions.
	 *
	 * @return void
	 */
	public function loadFacades()
	{
		require_once __DIR__.'/../../facades.php';
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = static::$app[static::getName()];

		return call_user_func_array(array($instance, $method), $parameters);
	}

}