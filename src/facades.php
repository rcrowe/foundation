<?php

use Illuminate\Foundation\Facade;

class Auth extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth'; }

}

class Blade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'blade'; }

}

class Cache extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'cache'; }

}

class Cookie extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'cookie'; }

}

class Crypt extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'encrypter'; }

}

class DB extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'db'; }

}

class On extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'controllers'; }

}

class Request extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'request'; }

}

class Response extends Facade {

	/**
	 * Return a new response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public static function make($content = '', $status = 200, $headers = array())
	{
		return static::$app->respond($content, $status, $headers);
	}

	/**
	 * Get the evaluated contents of the given view.
	 *
	 * @param  string  $view
	 * @param  array   $parameters
	 * @return string
	 */
	public static function show($view, array $parameters = array())
	{
		return static::$app->show($view, $parameters);
	}

	/**
	 * Convert some data into a JSON response.
	 *
	 * @param  mixed  $data
	 * @param  int    $status
	 * @param  array  $headers
	 * @return Symfony\Component\HttpFoundation\JsonResponse
	 */
	public static function json($data = array(), $status = 200, $headers = array())
	{
		return static::$app->json($data, $status, $headers);
	}

}

class Session extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'session'; }

}

class Validator extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'validator'; }

}