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
 * Get the root URL for the current request.
 *
 * @return string
 */
function root_url()
{
	return app()->request->root();
}

/**
 * Generate a path for the application.
 *
 * @param  string  $path
 * @param  array   $parameters
 * @param  bool    $secure
 * @return string
 */
function path($path = null, array $parameters = array(), $secure = null)
{
	$app = app();

	return $app['url']->to($path, $parameters, $secure);
}

/**
 * Generate a HTTPS path for the application.
 *
 * @param  string  $path
 * @param  array   $parameters
 * @return string
 */
function secure_path($path, array $parameters = array())
{
	return path($path, $parameters, true);
}

/**
 * Generate an asset path for the application.
 *
 * @param  string  $path
 * @param  bool    $secure
 * @return string
 */
function asset($path, $secure = null)
{
	$app = app();

	return $app['url']->asset($path, $secure);
}

/**
 * Generate an asset path for the application.
 *
 * @param  string  $path
 * @return string
 */
function secure_asset($path)
{
	return asset($path, true);
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

	return $app['url']->route($route, $parameters, $absolute);
}

/**
 * Generate a URL to a controller action.
 *
 * @param  string  $name
 * @param  string  $parameters
 * @param  bool    $absolute
 * @return string
 */
function action($name, $parameters = array(), $absolute = true)
{
	$app = app();

	return $app['url']->action($name, $parameters, $absolute);
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

	return $app['translator']->trans($id, $parameters, $domain, $locale);
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
function trans_choice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
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