<?php namespace Illuminate\Foundation;

use Illuminate\Support\Helpers;

class LightSwitch {

	/**
	 * Bootstrap the Illuminate framework.
	 *
	 * @return void
	 */
	public static function flip()
	{
		// Since we'll be using Underscore.php in a procedural style to avoid
		// E_STRICT errors, we will force the file to be loaded here since
		// Composer will not be resolving it lazily using the mappings.
		if ( ! class_exists('__'))
		{
			spl_autoload_call('__');
		}

		Helpers::register();

		require_once __DIR__.'/functions.php';

		// We will go ahead and create the Illuminate application since there
		// are no constructor arguments that are needed and we want to put
		// it into the $GLOBALS array to allow for some nicer functions.
		$application = new Application;

		set_app($application);

		return $application;
	}

}