<?php namespace Illuminate\Foundation\Facades;

use Illuminate\Support\Facades\Facade;

class Paginator extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'paginator'; }

}