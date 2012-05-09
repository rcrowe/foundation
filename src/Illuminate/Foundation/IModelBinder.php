<?php namespace Illuminate\Foundation;

use Symfony\Component\HttpFoundation\Request;

interface IModelBinder {

	/**
	 * Resolve a route model binding by ID.
	 *
	 * @param  mixed                                     $id
	 * @param  Symfony\Component\HttpFoundation\Request  $request
	 * @return mixed
	 */
	public function resolveBinding($id, Request $request);

}