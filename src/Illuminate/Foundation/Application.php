<?php namespace Illuminate\Foundation;

use Closure;
use Illuminate\Container;
use Illuminate\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Provider\ServiceProvider;

class Application extends Container {

	/**
	 * The application middlewares.
	 *
	 * @var array
	 */
	protected $middlewares = array();

	/**
	 * The pattern to middleware bindings.
	 *
	 * @var array
	 */
	protected $patternMiddlewares = array();

	/**
	 * The global middlewares for the application.
	 *
	 * @var array
	 */
	protected $globalMiddlewares = array();

	/**
	 * Create a new Illuminate application instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this['router'] = new Router;

		$this['request'] = Request::createFromGlobals();
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  Illuminate\Foundation\ServiceProvider  $provider
	 * @param  array  $options
	 * @return void
	 */
	public function register(ServiceProvider $provider, array $options = array())
	{
		$provider->register($this);

		foreach ($options as $key => $value)
		{
			$this[$key] = $value;
		}
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param  array   $environments
	 * @return string
	 */
	public function detectEnvironment(array $environments)
	{
		$base = $this['request']->getHost();

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
	 * Register a "before" application middleware.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function before(Closure $callback)
	{
		$this->globalMiddlewares['before'][] = $callback;
	}

	/**
	 * Register an "after" application middleware.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function after(Closure $callback)
	{
		$this->globalMiddlewares['after'][] = $callback;
	}

	/**
	 * Register a "finish" application middleware.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function finish(Closure $callback)
	{
		$this->globalMiddlewares['finish'][] = $callback;
	}

	/**
	 * Get the evaluated contents of the given view.
	 *
	 * @param  string  $view
	 * @param  array   $parameters
	 * @return string
	 */
	public function show($view, array $parameters = array())
	{
		return $this['blade']->show($view, $parameters);
	}

	/**
	 * Return a new response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function respond($content = '', $status = 200, $headers = array())
	{
		return new Response($content, $status, $headers);
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
		//

		return $this->redirect($url, $status);
	}

	/**
	 * Register a new middleware with the application.
	 *
	 * @param  string   $name
	 * @param  Closure  $callback
	 * @return void
	 */
	public function addMiddleware($name, Closure $callback)
	{
		$this->middlewares[$name] = $callback;
	}

	/**
	 * Get a registered middleware callback.
	 *
	 * @param  string   $name
	 * @return Closure
	 */
	public function getMiddleware($name)
	{
		if (array_key_exists($name, $this->middlewares))
		{
			return $this->middlewares[$name];
		}
	}

	/**
	 * Tie a registered middleware to a URI pattern.
	 *
	 * @param  string  $pattern
	 * @param  string  $name
	 * @return void
	 */
	public function matchMiddleware($pattern, $name)
	{
		$this->patternMiddlewares[$pattern][] = $name;
	}

	/**
	 * Handles the given request and delivers the response.
	 *
	 * @param  Illuminate\Foundation\Request  $request
	 * @return void
	 */
	public function run(Request $request = null)
	{
		// If no requests are given, we'll simply create one from PHP's global
		// variables and send it into the handle method, which will create
		// a Response that we can now send back to the client's browser.
		if (is_null($request))
		{
			$request = $this['request'];
		}

		// Once we have a request object, we will attempt to set the session
		// on the request so that the old input data may be retrieved by
		// the developer via the session through very simple methods.
		$this->prepareRequest($request);

		$response = $this->handle($request);

		$response->send();

		// Once we have successfully run the request, we can terminate it so
		// the request can be completely done and any final event may be
		// fired off by the applications before the request is ended.
		$this->terminate($request, $response);
	}

	/**
	 * Prepare the request by injecting any services.
	 *
	 * @param  Illuminate\Foundation\Request  $request
	 * @return Illuminate\Foundation\Request
	 */
	public function prepareRequest(Request $request)
	{
		if (isset($this['session']))
		{
			$request->setSessionStore($this['session']);
		}

		return $request;
	}

	/**
	 * Handle the given request and get the response.
	 *
	 * @param  Illuminate\Foundation\Request  $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	protected function handle(Request $request)
	{
		$route = $this['router']->match($request);

		return $route->run($request);
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