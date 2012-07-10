<?php namespace Illuminate\Foundation; use Closure;

class ControllerCollection extends \Silex\ControllerCollection {

	/**
	 * The currently grouped route attributes.
	 *
	 * @var array
	 */
	public $grouped = array();

	/**
	 * A keyed collection of available middlewares.
	 *
	 * @var array
	 */
	public $middlewares = array();

	/**
	 * The pattern based middleware assignments.
	 *
	 * @var array
	 */
	public $patterned = array();

	/**
	 * A keyed collection of wildcard assertion short-cuts.
	 *
	 * @var array
	 */
	protected $patterns = array(
		'#:' => '\d+',
	);

	/**
	 * The custom flags for the framework.
	 *
	 * @var array
	 */
	protected $flags = array('https', 'as', 'on', 'before');

	/**
	 * The Illuminate application instance.
	 *
	 * @var Illuminate\Foudnation\Application
	 */
	protected $application;

	/**
	 * Create a new Illuminate controller collection.
	 *
	 * @param  Illuminate\Foundation\Application  $application
	 * return  void
	 */
	public function __construct($application)
	{
		parent::__construct($application['route_factory']);

		$this->application = $application;
	}

	/**
	 * Register a route with the application.
	 *
	 * @param  string            $pattern
	 * @param  mixed             $to
	 * @return Silex\Controller
	 */
	public function match($pattern, $to)
	{
		list($pattern, $asserts) = $this->formatPattern($pattern);

		// If the given "to" is just a Closure we'll just go ahead and convert it
		// into an array so we can code all registrations the same. This makes
		// the registration coding more consistent and simpler on this side.
		if ($to instanceof Closure) $to = array($to);

		if (count($this->grouped) > 0)
		{
			$to = array_merge(end($this->grouped), $to);
		}

		// Next we'll need to find the callable Closure in the routes array which
		// should just be the value that is a Closure instance in given array.
		// Once we have it we will call base "match" method to register it.
		$callable = $this->findCallable($to);

		$controller = parent::match($pattern, $callable);

		$this->handleRouteFlags($to, $controller);

		// Next we will check for any pattern based middlewares that could apply
		// to this route. This allows the developer to easily set any of the
		// middlewares to apply to all routes having a given request URI.
		$this->applyPatternFilters($pattern, $controller);

		foreach ($asserts as $key => $pattern)
		{
			$controller->assert($key, $pattern);
		}

		return $controller;
	}

	/**
	 * Find the callable Closure from a route array.
	 *
	 * @param  array    $to
	 * @return Closure
	 */
	protected function findCallable($to)
	{
		return __($to)->find(function($value)
		{
			return $value instanceof Closure;
		});
	}

	/**
	 * Handle the setup of the custom framework ruote flags.
	 *
	 * @param  array  $to
	 * @param  Silex\Controller  $controller
	 * @return void
	 */
	protected function handleRouteFlags($to, $controller)
	{
		foreach ($this->flags as $flag)
		{
			// Each flag handles a short-cut into the Silex routing engine and just
			// provides a cleaner way to interact with the various options that
			// are available for each route like filters, names, HTTPS, etc.
			if (isset($to[$flag]))
			{
				$this->{"defineRoute".ucwords($flag)}($to, $controller);
			}
		}
	}

	/**
	 * Handle the "https" route flag.
	 *
	 * @param  array  $to
	 * @param  Silex\Controller  $controller
	 * @return void
	 */
	protected function defineRouteHttps($to, $controller)
	{
		$scheme = $to['https'] ? 'requireHttps' : 'requireHttp';

		$controller->$scheme();
	}

	/**
	 * Handle the "as" route flag.
	 *
	 * @param  array  $to
	 * @param  Silex\Controller  $controller
	 * @return void
	 */
	protected function defineRouteAs($to, $controller)
	{
		$controller->bind($to['as']);
	}

	/**
	 * Handle the "on" route flag.
	 *
	 * @param  array  $to
	 * @param  Silex\Controller  $controller
	 * @return void
	 */
	protected function defineRouteOn($to, $controller)
	{
		$controller->method(strtoupper($to['on']));
	}

	/**
	 * Handle the "before" route flag.
	 *
	 * @param  array  $to
	 * @param  Silex\Controller  $controller
	 * @return void
	 */
	protected function defineRouteBefore($to, $controller)
	{
		foreach (explode('|', $to['before']) as $m)
		{
			$controller->before($this->middlewares[$m]);
		}
	}

	/**
	 * Apply any pattern based filter to a route.
	 *
	 * @param  string  $pattern
	 * @param  Silex\Controller  $controller
	 * @return void
	 */
	protected function applyPatternFilters($pattern, $controller)
	{
		// To apply pattern based filters we will just iterate through each of the
		// patterns that have been registered with the collection and check if
		// they match the route being registered. If they do, we apply them.
		foreach ($this->patterned as $key => $value)
		{
			if (str_is($key, $pattern))
			{
				$controller->before($this->middlewares[$value]);
			}
		}
	}

	/**
	 * Format the URI pattern for a route.
	 *
	 * @param  string  $pattern
	 * @return array
	 */
	protected function formatPattern($pattern)
	{
		$asserts = array();

		preg_match_all('/\{(#:)(.+)\}/', $pattern, $matches);

		// Once we have an array of all of the matches, we can simple trim down
		// the short-cut operator and add an assert with the proper regular
		// expressions that performs the functionality of the shortcuts.
		foreach ($matches[0] as $key => $match)
		{
			$pattern = str_replace($match, '{'.$matches[2][$key].'}', $pattern);

			$asserts[$matches[2][$key]] = $this->patterns[$matches[1][$key]];
		}

		return array($pattern, $asserts);		
	}

	/**
	 * Register a route group with shared attributes.
	 *
	 * @param  Illuminate\Application  $application
	 * @param  array                   $attributes
	 * @param  Closure                 $callback
	 * @return void
	 */
	public function group(array $attributes, Closure $callback)
	{
		$this->grouped[] = $attributes;

		$callback($this->application);

		array_pop($this->grouped);
	}

	/**
	 * Get a given middleware Closure.
	 *
	 * @param  string   $name
	 * @return Closure
	 */
	public function getMiddleware($name)
	{
		if (isset($this->middlewares[$name]))
		{
			return $this->middlewares[$name];
		}
	}

	/**
	 * Register a middleware with the application.
	 *
	 * @param  string   $name
	 * @param  Closure  $middleware
	 * @return void
	 */
	public function addMiddleware($name, Closure $middleware)
	{
		$this->middlewares[$name] = $middleware;
	}

	/**
	 * Assigns a URI pattern to a named middleware.
	 *
	 * @param  string   $middleware
	 * @param  string   $pattern
	 * @return void
	 */
	public function matchMiddleware($middleware, $pattern)
	{
		$this->patterned[$pattern] = $middleware;
	}

	/**
	 * Get the Illuminate application instance.
	 *
	 * @return Illuminate\Application
	 */
	public function getApplication()
	{
		return $this->application;
	}

}