<?php namespace Illuminate\Foundation;

class LightSwitch {

	/**
	 * Indicates if the light switch is on.
	 *
	 * @var bool
	 */
	protected static $on = false;

	/**
	 * Bootstrap the Illuminate framework.
	 *
	 * @return void
	 */
	public static function flip()
	{
		if (static::$on) return;

		// Since we'll be using Underscore.php in a procedural style to
		// avoid E_STRICT errors, we'll force the file to be loaded
		// here since Composer won't be resolving it lazily.
		if ( ! class_exists('__'))
		{
			spl_autoload_call('__');
		}

		static::$on = true;
	}

}