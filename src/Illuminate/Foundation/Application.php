<?php namespace Illuminate\Foundation;

use Closure;
use ArrayAccess;
use Illuminate\Container;
use Illuminate\Session\TokenMismatchException;
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

		// Illuminate extends the default controller collections to add an array
		// of additional functionality and short-cuts to the class, so we'll
		// override the default registration in the container with ours.
		$this['controllers'] = $this->share(function() use ($app)
		{
			return new ControllerCollection($app);
		});

		$this->addCoreMiddlewares();
	}

	/**
	 * Register the core framework middlewares.
	 *
	 * @return void
	 */
	protected function addCoreMiddlewares()
	{
		foreach (array('Auth', 'Csrf') as $middleware)
		{
			$this->{"add{$middleware}Middleware"}();
		}
	}

	/**
	 * Register the Auth middleware for the application.
	 *
	 * @return void
	 */
	protected function addAuthMiddleware()
	{
		$app = $this;

		// The "auth" middleware provides a convenient way to verify that a given
		// user is logged into the application. If they are not, we will just
		// redirect the users to the "login" named route as a convenience.
		$this->addMiddleware('auth', function() use ($app)
		{
			if ($app['auth']->isGuest())
			{
				return $app->redirectToRoute('login');
			}
		});
	}

	/**
	 * Register the CSRF middleware for the application.
	 *
	 * @return void
	 */
	protected function addCsrfMiddleware()
	{
		$app = $this;

		// The "csrf" middleware provides a simple middleware for checking that a
		// CSRF token in the request inputs matches the CSRF token stored for
		// the user in the session data. If it doesn't, we will bail out.
		$this->addMiddleware('csrf', function() use ($app)
		{
			$token = $app['session']->getToken();

			if ($token !== $app['request']->get('csrf_token'))
			{
				throw new TokenMismatchException;
			}
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
			// To determine the current environment, we'll simply iterate through
			// the possible environments and look for a host that matches our
			// host in the requests context, then return that environment.
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
	 * Retrieve an input item from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function input($key = null, $default = null)
	{
		return $this['request']->input($key, $default);
	}

	/**
	 * Retrieve an old input item.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function old($key = null, $default = null)
	{
		return $this['request']->old($key, $default);
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

		if (isset($this['session']))
		{
			$redirect->setSession($this['session']);
		}

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

		return $this->redirect($url, $status);
	}

	/**
	 * Register a named middleware with the application.
	 *
	 * @param  string   $name
	 * @param  Closure  $middleware
	 * @return void
	 */
	public function addMiddleware($name, Closure $middleware)
	{
		return $this['controllers']->addMiddleware($name, $middleware);
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
		return $this['controllers']->matchMiddleware($middleware, $pattern);
	}

	/**
	 * Get a given middleware Closure.
	 *
	 * @param  string   $name
	 * @return Closure
	 */
	public function getMiddleware($name)
	{
		return $this['controllers']->getMiddleware($name);
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
	 * Handles the given request and delivers the response.
	 *
	 * @param  Symfony\Component\HttpFoundation\Request  $request
	 * @return void
	 */
	public function run(SymfonyRequest $request = null)
	{
		// If no requests are given, we'll simply create one from PHP's global
		// variables and send it into the handle method, which will create
		// a Response that we can now send back to the client's browser.
		if (is_null($request))
		{
			$request = Request::createFromGlobals();
		}

		// Once we have a request object, we will attempt to set the session
		// on the request so that the old input data may be retrieved by
		// the developer via the session through very simple methods.
		$this->prepareRequest($request);

		$response = $this->handle($request);

		$response->send();

		// Once we have successfully run the request, we can terminate it so
		// the request can be completely done and any final event may be
		// fired off by the application before the request is ended.
		$this->terminate($request, $response);
	}

	/**
	 * Prepare the request by injecting any services.
	 *
	 * @param  Symfony\Component\HttpFoundation\Request
	 * @return Symfony\Component\HttpFoundation\Request
	 */
	public function prepareRequest(SymfonyRequest $request)
	{
		if (isset($this['session']))
		{
			$request->setSessionStore($this['session']);
		}

		return $request;
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
		if (strpos($method, 'redirectTo') === 0)
		{
			array_unshift($parameters, strtolower(substr($method, 10)));

			return call_user_func_array(array($this, 'redirectToRoute'), $parameters);
		}

		throw new \BadMethodCallException("Call to undefined method {$method}.");
	}

}