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
 * @param  bool    $https
 * @return string
 */
function path($path = null, $https = null)
{
	$request = app()->request;

	// If no scheme is specified we will use the scheme for the current request,
	// otherwise we will used the scheme the consumer of the function asked
	// for, either http or https. Then we will add the path to the URLs.
	if (is_null($https))
	{
		$scheme = $request->getScheme().'://';
	}
	elseif ($https)
	{
		$scheme = 'https://';
	}
	else
	{
		$scheme = 'http://';
	}

	return $scheme.$request->getHttpHost().$request->getBasePath().'/'.$path;
}

/**
 * Generate a HTTP path for the application.
 *
 * @param  string  $path
 * @return string
 */
function http_path($path)
{
	return path($path, false);
}

/**
 * Generate a HTTPS path for the application.
 *
 * @param  string  $path
 * @return string
 */
function https_path($path)
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

/**
 * Flatten a multi-dimensional associative array with dots.
 *
 * @param  array   $array
 * @param  string  $prepend
 * @return array
 */
function array_dot($array, $prepend = '')
{
	$results = array();

	foreach ($array as $key => $value)
	{
		if (is_array($value))
		{
			$results = array_merge($results, array_dot($value, $prepend.$key.'.'));
		}
		else
		{
			$results[$prepend.$key] = $value;
		}
	}

	return $results;
}

/**
 * Determine if a string starts with a given needle.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function starts_with($haystack, $needle)
{
	return strpos($haystack, $needle) === 0;
}

/**
 * Determine if a given string matches a given pattern.
 *
 * @param  string  $pattern
 * @param  string  $value
 * @return bool
 */
function str_is($pattern, $value)
{
	// Asterisks are translated into zero-or-more regular expression wildcards
	// to make it convenient to check if the strings starts with the given
	// pattern such as "library/*". Useful for checking basic strings.
	if ($pattern !== '/')
	{
		$pattern = str_replace('*', '(.*)', $pattern).'\z';
	}
	else
	{
		$pattern = '^/$';
	}

	return (bool) preg_match('#'.$pattern.'#', $value);
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed  $value
 * @return mixed
 */
function value($value)
{
	return $value instanceof Closure ? $value() : $value;
}