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

/**
 * Translate the given message.
 *
 * @param  string  $id
 * @param  array   $parameters
 * @param  string  $domain
 * @param  string  $locale
 * @return string
 */
function trans($id, $parameters = array(), $domain = null, $locale = null)
{
	$app = app();

	if (isset($app['translator']))
	{
		return $app['translator']->trans($id, $parameters, $domain, $locale);
	}
	else
	{
		throw new RuntimeException("Application translator not set.");
	}
}

/**
 * Translates the given message based on a count.
 *
 * @param  string  $id
 * @param  int     $number
 * @param  array   $parameters
 * @param  string  $domain
 * @param  string  $locale
 * @return string
 */
function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
{
	$app = app();

	if (isset($app['translator']))
	{
		return $app['translator']->transChoice($id, $number, $parameters, $domain, $locale);
	}
	else
	{
		throw new RuntimeException("Application translator not set.");
	}
}