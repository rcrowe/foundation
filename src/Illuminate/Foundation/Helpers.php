<?php

/**
 * Generate a URL to a named route.
 *
 * @param  string  $route
 * @return string
 */
function route($route)
{
	return $GLOBALS['__illuminate.app']['url_generator']->generate($route);
}