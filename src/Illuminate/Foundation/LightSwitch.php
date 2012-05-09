<?php namespace Illuminate\Foundation;

class LightSwitch {

	/**
	 * Bootstrap the Illuminate framework.
	 *
	 * @return void
	 */
	public static function on()
	{
		require_once __DIR__.'/silex.phar';
	}

}