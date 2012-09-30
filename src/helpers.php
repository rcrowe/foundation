<?php

/**
 * Define the Laravel version number.
 */
define('LARAVEL_VERSION', '4.0.0');

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
 * Get the CSRF token value.
 *
 * @return string
 */
function csrf_token()
{
	$app = app();

	if (isset($app['session']))
	{
		return $app['session']->getToken();
	}
	else
	{
		throw new RuntimeException("Application session store not set.");
	}
}

/**
 * Generate a path for the application.
 *
 * @param  string  $path
 * @param  bool    $secure
 * @return string
 */
function path($path = null, $secure = false)
{
	$app = app();

	return $app['url.generator']->to($path, $secure);
}

/**
 * Generate a HTTPS path for the application.
 *
 * @param  string  $path
 * @return string
 */
function secure_path($path)
{
	return path($path, true);
}

/**
 * Get the root URL for the current request.
 *
 * @return string
 */
function root_url()
{
	return app()->request->getRootUrl();
}

/**
 * Generate a URL to a named route.
 *
 * @param  string  $route
 * @param  string  $parameters
 * @param  bool    $absolute
 * @return string
 */
function route($route, $parameters = array(), $absolute = true)
{
	$app = app();

	return $app['url.generator']->route($route, $parameters, $absolute);
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
function trans($id, $parameters = array(), $domain = 'messages', $locale = null)
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
function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
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