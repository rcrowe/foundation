<?php

/**
 * Generate a URL to a named route.
 *
 * @param  string  $route
 * @return string
 */
function path($route)
{
	return $GLOBALS['__illuminate.app']['url_generator']->generate($route);
}