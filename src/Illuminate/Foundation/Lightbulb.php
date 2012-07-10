<?php namespace Illuminate\Foundation;

class Lightbulb {

	/**
	 * Bootstrap the Illuminate framework.
	 *
	 * @return void
	 */
	public static function on()
	{
		if ( ! class_exists('__')) spl_autoload_call('__');

		require_once __DIR__.'/../../helpers.php';

		// We will go ahead and create the Illuminate application since there
		// are no constructor arguments that are needed and we want to put
		// it into the $GLOBALS array to allow for some nicer functions.
		$application = new Application;

		set_app($application);

		return $application;
	}

}