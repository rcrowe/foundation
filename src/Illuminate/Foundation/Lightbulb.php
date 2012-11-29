<?php namespace Illuminate\Foundation;

class Lightbulb {

	/**
	 * Bootstrap the Illuminate framework.
	 *
	 * @return void
	 */
	public static function on()
	{
		// We will go ahead and create the Illuminate application since there
		// are no constructor arguments that are needed and we want to put
		// it into the $GLOBALS array to allow for some nicer functions.
		set_app($application = new Application);

		return $application;
	}

}