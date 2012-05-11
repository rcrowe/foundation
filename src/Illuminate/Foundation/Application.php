<?php namespace Illuminate\Foundation;

use Closure;
use ArrayAccess;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Application extends \Silex\Application implements ArrayAccess {

	/**
	 * The Illuminate container instance.
	 *
	 * @var Illuminate\Container
	 */
	public $container;

	/**
	 * Create a new Illuminate application.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->container = new Container;

		parent::__construct();

		$app = $this;

		// We will register the URL service provider by default as this is quite
		// commonly used when building any non-trivial application and named
		// routes are very useful for keeping codes flexible and simple.
		$this->register(new UrlGeneratorServiceProvider);

		$this['controllers'] = $this->share(function() use ($app)
		{
			return new ControllerCollection($app);
		});

		// The Illuminate event dispatcher provides a simpler, yet powerful way
		// to build de-coupled systems using events, including queueing and
		// flushing events. We'll go ahead and register a shared object.
		$this['events'] = $this->share(function() use ($app)
		{
			return new Dispatcher;
		});
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param  array   $environments
	 * @return string
	 */
	public function detectEnvironment(array $environments)
	{
		$base = $this['request_context']->getHost();

		foreach ($environments as $environment => $hosts)
		{
			// To determine the current environment, we'll simply spin through
			// the possible environments and look for a host that matches a
			// host in the request context, then return that environment.
			foreach ($hosts as $host)
			{
				if (str_is($base, $host))
				{
					return $this['env'] = $environment;
				}
			}
		}

		return $this['env'] = 'default';
	}

	/**
	 * Register a route group with shared attributes.
	 *
	 * @param  array    $attributes
	 * @param  Closure  $callback
	 * @return void
	 */
	public function group(array $attributes, Closure $callback)
	{
		return $this['controllers']->group($attributes, $callback);
	}

	/**
	 * Generate a RedirectResponse to another URL.
	 *
	 * @param  string   $url
	 * @param  int      $status
	 * @return Illuminate\RedirectResponse
	 */
	public function redirect($url, $status = 302)
	{
		$redirect = new RedirectResponse($url, $status);

		// @todo: set session instance on redirect to allow for "with" methods...

		return $redirect;
	}

	/**
	 * Create a redirect response to a named route.
	 *
	 * @param  string  $route
	 * @param  array   $parameters
	 * @param  int     $status
	 * @return Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function redirectToRoute($route, $parameters = array(), $status = 302)
	{
		$url = $this['url_generator']->generate($route, $parameters);

		return parent::redirect($url, $status);
	}

	/**
	 * Register a model binder with the application.
	 *
	 * @param  string                  $wildcard
	 * @param  mixed                   $binder
	 * @return Illuminate\Application
	 */
	public function modelBinder($wildcard, $binder)
	{
		return $this['controllers']->modelBinder($wildcard, $binder);
	}

	/**
	 * Register an array of model binders with the application.
	 *
	 * @param  array  $binders
	 * @return void
	 */
	public function modelBinders(array $binders)
	{
		return $this['controllers']->modelBinders($binders);
	}

	/**
	 * Register a middleware with the application.
	 *
	 * @param  string   $name
	 * @param  Closure  $middleware
	 * @return void
	 */
	public function middleware($name, Closure $middleware)
	{
		return $this['controllers']->middleware($name, $middleware);
	}

	/**
	 * Create a new mountable controller collection.
	 *
	 * @return Illuminate\Foundation\ControllerCollection
	 */
	public function newMountable()
	{
		return new ControllerCollection($this);
	}

	/**
	 * Hnadles the given request and delivers the response.
	 *
	 * @param  Symfony\Component\HttpFoundation\Request  $request
	 * @return void
	 */
	public function run(SymfonyRequest $request = null)
	{
		// If not request is given, we'll simply create one from PHP's global
		// variables and send it into the handle method, which will create
		// a Response that we can now send back to the client's browser.
		if (is_null($request))
		{
			$request = Request::createFromGlobals();
		}

		$response = $this->handle($request);

		$response->send();

		$this->terminate($request, $response);
	}

	/**
	 * Register a binding with the container.
	 *
	 * @param  string               $abstract
	 * @param  Closure|string|null  $concrete
	 * @param  bool                 $shared
	 * @return void
	 */
	public function bind($abstract, $concrete = null, $shared = false)
	{
		return $this->container->bind($abstract, $concrete, $shared);
	}

	/**
	 * Register a shared binding in the container.
	 *
	 * @param  string               $abstract
	 * @param  Closure|string|null  $concrete
	 * @return void
	 */
	public function sharedBinding($abstract, $concrete = null)
	{
		return $this->container->sharedBinding($abstract, $concrete);
	}

	/**
	 * Register an existing instance as shared in the container.
	 *
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * @return void
	 */
	public function instance($abstract, $instance)
	{
		return $this->container->instance($abstract, $instance);
	}

	/**
	 * Alias a type to a shorter name.
	 *
	 * @param  string  $abstract
	 * @param  string  $alias
	 * @return void
	 */
	public function alias($abstract, $alias)
	{
		return $this->container->alias($abstract, $value);
	}

	/**
	 * Determine if a value exists by offset.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return isset($this->container[$key]);
	}

	/**
	 * Get a value by offset.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->container[$key];
	}

	/**
	 * Set a value by offset.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->container[$key] = $value;
	}

	/**
	 * Unset a value by offset.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->container[$key]);
	}

	/**
	 * Dynamically access application services.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this[$key];
	}

	/**
	 * Dynamically set application services.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this[$key] = $value;
	}

	/**
	 * Dynamically handle application method calls.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (starts_with($method, 'redirectTo'))
		{
			array_unshift($parameters, strtolower(substr($method, 10)));

			return call_user_func_array(array($this, 'redirectToRoute'), $parameters);
		}

		throw new \BadMethodCallException("Call to undefined method {$method}.");
	}

}