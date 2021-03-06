<?php namespace Illuminate\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class Redirect extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'redirect'; }

}