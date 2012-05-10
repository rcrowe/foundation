<?php

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
 * Create a new URL for the given URI.
 *
 * @param  string  $uri
 * @return string
 */
function path($uri)
{
	return app()->getRootUrl().'/'.$uri;
}

/**
 * Create a new asset URL for the given URI.
 *
 * @param  string  $uri
 * @return string
 */
function asset($uri)
{
	return path('assets/'.$uri);
}

/**
 * Generate a URL to a named route.
 *
 * @param  string  $route
 * @return string
 */
function route($route)
{
	return app()->url_generator->generate($route);
}