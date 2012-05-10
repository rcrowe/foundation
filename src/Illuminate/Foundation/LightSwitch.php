<?php namespace Illuminate\Foundation;

class LightSwitch {

	/**
	 * Bootstrap the Illuminate framework.
	 *
	 * @return void
	 */
	public static function flip()
	{
		// Since we'll be using Underscore.php in a procedural style to
		// avoid E_STRICT errors, we'll force the file to be loaded
		// here since Composer will not be resolving it lazily.
		if ( ! class_exists('__'))
		{
			spl_autoload_call('__');
		}

		require_once __DIR__.'/Helpers.php';

		// We will go ahead and create the Illuminate application since
		// no constructor arguments are needed and we want to put it
		// in the $GLOBALS array to allow for some nice functions.
		$application = new Application;

		$GLOBALS['__illuminate.app'] = $application;

		return $application;
	}

}