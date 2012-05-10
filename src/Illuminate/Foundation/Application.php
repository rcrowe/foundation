<?php namespace Illuminate\Foundation;

use Closure;
use ArrayAccess;
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
		// routes are very useful for keeping code flexible and simple.
		$this->register(new UrlGeneratorServiceProvider);

		$this['controllers'] = $this->share(function() use ($app)
		{
			return new ControllerCollection($app);
		});

		// The Symfony YAML parser is used to conveniently parse the application
		// configuration files for the default and local environemnts so we
		// can go ahead and register a shared instance here for our use.
		$this['yaml.parser'] = $this->share(function()
		{
			return new \Symfony\Component\Yaml\Parser;
		});
	}

	/**
	 * Detect the application's current.
	 *
	 * @param  array   $environments
	 * @return string
	 */
	public function registerEnvironment(array $environments)
	{
		$base = $this['request_context']->getHost();

		foreach ($environments as $environment => $config)
		{
			if ( ! isset($config['hosts'])) continue;

			// To determine the current environment, we'll simply spin through
			// the possible environments and look for a host that matches a
			// host in the request context, then return that environment.
			foreach ($config['hosts'] as $host)
			{
				if ($host === $base)
				{
					return $this->loadEnvironment($environments, $environment);
				}
			}
		}
		
		return $this->loadEnvironment($environments);
	} 

	/**
	 * Load the given environment configuration.
	 *
	 * @param  array   $environment
	 * @param  string  $environment
	 * @return void
	 */
	protected function loadEnvironment(array $environments, $environment = null)
	{
		$merge = array();

		$yaml = $this['yaml.parser'];

		// First, if an environment is being loaded besides the default, we will load
		// the configuration for that environment. This allows the easy cascading
		// of configuration options from environments to the default options.
		if ( ! is_null($environment))
		{
			$file = $environments[$environment]['config'];

			$merge = $yaml->parse(file_get_contents($file));
		}

		// Once we have the environment configuration, we will load the default file
		// and merge it with the environments. Then we'll set each option on the
		// application instance so they can be easily accessed via the code.
		$file = $environments['default']['config'];

		$base = $yaml->parse(file_get_contents($file));

		foreach (array_dot(array_merge($base, $merge)) as $key => $value)
		{
			$this[$key] = $value;
		}
	}

	/**
	 * Register the root route for the application.
	 *
	 * @param  mixed             $to
	 * @return Silex\Controller
	 */
	public function root($to)
	{
		return $this['controllers']->root($to);
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
	 * Create a redirect response to a named route.
	 *
	 * @param  string  $route
	 * @param  array   $parameters
	 * @param  int     $status
	 * @return Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function redirect_to_route($route, $parameters = array(), $status = 302)
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
		if (starts_with($method, 'redirect_to_'))
		{
			array_unshift($parameters, substr($method, 12));

			return call_user_func_array(array($this, 'redirect_to_route'), $parameters);
		}

		throw new \BadMethodCallExcpeption("Call to undefined method {$method}.");
	}

}