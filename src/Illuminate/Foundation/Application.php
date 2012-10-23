<?php namespace Illuminate\Foundation;

use Closure;
use Illuminate\Container;
use Illuminate\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Application extends Container implements HttpKernelInterface {

	/**
	 * Indicates if the application has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * All of the registered service providers.
	 *
	 * @var array
	 */
	protected $serviceProviders = array();

	/**
	 * Create a new Illuminate application instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this['request'] = Request::createFromGlobals();

		$this->register(new Providers\RoutingServiceProvider);

		// The exception handler class takes care of determining which of the bound
		// exception handler Closures should be called for a given exception and
		// gets the response from them. We'll bind it here to allow overrides.
		$this->register(new Providers\ExceptionServiceProvider);
	}

	/**
	 * Start the exception handling for the request.
	 *
	 * @return void
	 */
	public function startExceptionHandling()
	{
		$provider = array_first($this->serviceProviders, function($key, $provider)
		{
			return $provider instanceof Providers\ExceptionServiceProvider;
		});

		$provider->startHandling($this);
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

		// First we will check to see if we have any command-line arguments and if
		// if we do we will set this environment based on those arguments as we
		// need to set it before each configurations are actually loaded out.
		$arguments = $this['request']->server->get('argv');

		if (count($arguments) > 0)
		{
			return $this->detectConsoleEnvironment($arguments);
		}

		foreach ($environments as $environment => $hosts)
		{
			// To determine the current environment, we'll simply iterate through the
			// possible environments and look for a host that matches this host in
			// the request's context, then return back that environment's names.
			foreach ($hosts as $host)
			{
				if (str_is($host, $base) or $this->isMachine($host))
				{
					return $this['env'] = $environment;
				}
			}
		}

		return $this['env'] = 'default';
	}

	/**
	 * Set the application environment from command-line arguments.
	 *
	 * @param  array   $arguments
	 * @return string
	 */
	protected function detectConsoleEnvironment(array $arguments)
	{
		foreach ($arguments as $key => $value)
		{
			// For the console environmnet, we'll just look for an argument that starts
			// with "--env" then assume that it is setting the environment for every
			// operation being performed, and we'll use that environment's config.
			if (starts_with($value, '--env='))
			{
				$segments = array_slice(explode('=', $value), 1);

				return $this['env'] = $segments[0];
			}
		}

		return $this['env'] = 'default';
	}

	/**
	 * Determine if the name matches the machine name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	protected function isMachine($name)
	{
		return gethostname() == $name;
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  Illuminate\Foundation\Providers\ServiceProvider  $provider
	 * @param  array  $options
	 * @return void
	 */
	public function register(ServiceProvider $provider, array $options = array())
	{
		$provider->register($this);

		// Once we have registered the service we will iterate through the options
		// and set each of them on the application so they will be available on
		// the actual loading of the service objects and for developer usage.
		foreach ($options as $key => $value)
		{
			$this[$key] = $value;
		}

		$this->serviceProviders[] = $provider;
	}

	/**
	 * Register a "before" application filter.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function before(Closure $callback)
	{
		return $this['router']->before($callback);
	}

	/**
	 * Register an "after" application filter.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function after(Closure $callback)
	{
		return $this['router']->after($callback);
	}

	/**
	 * Register a "close" application filter.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function close(Closure $callback)
	{
		return $this['router']->close($callback);
	}

	/**
	 * Register a "finish" application filter.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function finish(Closure $callback)
	{
		$this['router']->finish($callback);
	}

	/**
	 * Handles the given request and delivers the response.
	 *
	 * @return void
	 */
	public function run()
	{
		$response = $this->dispatch($this['request']);

		$response->send();

		$this['router']->callFinishFilter($this['request'], $response);
	}

	/**
	 * Handle the given request and get the response.
	 *
	 * @param  Illuminate\Foundation\Request  $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function dispatch(Request $request)
	{
		// Before we handle the requests we need to make sure the application has been
		// booted up. The boot process will call the "boot" method on each service
		// provider giving them all a chance to register any application events.
		if ( ! $this->booted) $this->boot();

		return $this['router']->dispatch($this->prepareRequest($request));
	}

	/**
	 * Handle the given request and get the response.
	 *
	 * Provides compatibility with BrowserKit functional testing.
	 *
	 * @implements HttpKernelInterface::handle
	 *
	 * @param  Illuminate\Foundation\Request  $request
	 * @param  int   $type
	 * @param  bool  $catch
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		return $this->dispatch($request);
	}

	/**
	 * Boot the application's service providers.
	 *
	 * @return void
	 */
	protected function boot()
	{
		foreach ($this->serviceProviders as $provider)
		{
			$provider->boot($this);
		}

		$this->booted = true;
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
	 * Prepare the given value as a Response object.
	 *
	 * @param  mixed  $value
	 * @param  Illuminate\Foundation\Request  $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function prepareResponse($value, Request $request)
	{
		if ( ! $value instanceof SymfonyResponse) $value = new Response($value);

		return $value->prepare($request);
	}

	/**
	 * Register a new filter with the router.
	 *
	 * @param  string   $name
	 * @param  Closure  $callback
	 * @return void
	 */
	public function addFilter($name, Closure $callback)
	{
		return $this['router']->addFilter($name, $callback);
	}

	/**
	 * Get a registered filter callback.
	 *
	 * @param  string   $name
	 * @return Closure
	 */
	public function getFilter($name)
	{
		return $this['router']->getFilter($name);
	}

	/**
	 * Throw an HttpException with the given data.
	 *
	 * @param  int     $code
	 * @param  string  $message
	 * @param  array   $headers
	 * @return void
	 */
	public function abort($code, $message = '', array $headers = array())
	{
		throw new HttpException($code, $message, null, $headers);
	}

	/**
	 * Register an application error handler.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function error(Closure $callback)
	{
		$this['exception']->error($callback);
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

}