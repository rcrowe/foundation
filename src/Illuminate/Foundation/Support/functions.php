<?php

/**
 * Set the globally available application instance.
 *
 * @param  Illuminate\Foundation\Application  $application
 * @return void
 */
function set_app($application)
{
	$GLOBALS['__illuminate.app'] = $application;
}

/**
 * Get the globally available request instance.
 *
 * @return Illuminate\Foundation\Application
 */
function app()
{
	return $GLOBALS['__illuminate.app'];
}

/**
 * Generate a URL to a named route.
 *
 * @param  string  $route
 * @param  string  $parameters
 * @param  bool    $absolute
 * @return string
 */
function route($route, $parameters = array(), $absolute = false)
{
	return app()->url_generator->generate($route, $parameters, $absolute);
}