<?php namespace Illuminate\Foundation;

use Closure;
use ArrayAccess;
use Illuminate\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Application extends Container implements ArrayAccess {

	/**
	 * The Illuminate container instance.
	 *
	 * @var Illuminate\Container
	 */
	public $container;

	/**
	 * Create a new Illuminate application instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this['request'] = Request::createFromGlobals();
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
		//

		return $this->redirect($url, $status);
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