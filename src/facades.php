<?php

use Illuminate\Support\Facade;

class App extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return static::$app; }

}

class Artisan extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'artisan'; }

}

class Auth extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth'; }

}

class Cache extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'cache'; }

}

class Config extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'config'; }

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

class Event extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'events'; }

}

class File extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'files'; }

}

class Hash extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'hash'; }

}

class Input extends Facade {

	/**
	 * Get an item from the input data.
	 *
	 * This method is used for all request verbs (GET, POST, PUT, and DELETE)
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public static function get($key = null, $default = null)
	{
		return static::$app['request']->input($key, $default);
	}

	/**
	 * Get all of the input data for the request, including files.
	 *
	 * @return array
	 */
	public static function all()
	{
		return static::$app['request']->everything();
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'request'; }

}

class Lang extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'translator'; }

}

class Log extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'log'; }

}

class Mail extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'mailer'; }

}

class Paginator extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'paginator'; }

}

class Redirect extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'redirect'; }

}

class Redis extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'redis'; }

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
	public function make($content = '', $status = 200, array $headers = array())
	{
		return new Illuminate\Http\Response($content, $status, $headers);
	}

	/**
	 * Return a new JSON response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function json($data = array(), $status = 200, array $headers = array())
	{
		return new Symfony\Component\HttpFoundation\JsonResponse($data, $status, $headers);
	}

	/**
	 * Return a new streamed response from the application.
	 *
	 * @param  Closure  $callback
	 * @param  int      $status
	 * @param  array    $headers
	 * @return Symfony\Component\HttpFoundation\StreamedResponse
	 */
	public function stream($callback, $status = 200, array $headers = array())
	{
		return new Symfony\Component\HttpFoundation\StreamedResponse($callback, $status, $headers);
	}

}

class Route extends Facade {

	/**
	 * Register a new filter with the application.
	 *
	 * @param  string   $name
	 * @param  Closure|string  $callback
	 * @return void
	 */
	public static function filter($name, $callback)
	{
		return static::$app['router']->addFilter($name, $callback);
	}

	/**
	 * Tie a registered middleware to a URI pattern.
	 *
	 * @param  string  $pattern
	 * @param  string|array  $name
	 * @return void
	 */
	public static function when($pattern, $name)
	{
		return static::$app['router']->matchFilter($pattern, $name);
	}

	/**
	 * Determine if the current route matches a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function is($name)
	{
		return static::$app['router']->currentRouteNamed($name);
	}

	/**
	 * Determine if the current route uses a given controller action.
	 *
	 * @param  string  $action
	 * @return bool
	 */
	public static function uses($action)
	{
		return static::$app['router']->currentRouteUses($action);
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'router'; }

}

class Schema extends Facade {

	/**
	 * Get a schema builder instance for a connection.
	 *
	 * @param  string  $name
	 * @return Illuminate\Database\Schema\Builder
	 */
	public static function connection($name)
	{
		return static::$app['db']->connection($name)->getSchemaBuilder();
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return static::$app['db']->connection()->getSchemaBuilder();
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

class URL extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'url'; }

}

class Validator extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'validator'; }

}

class View extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'view'; }

}