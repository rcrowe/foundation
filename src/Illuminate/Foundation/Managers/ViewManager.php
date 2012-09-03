<?php namespace Illuminate\Foundation\Managers;

use Twig_Environment;
use Illuminate\View\PhpEngine;
use Illumiante\View\TwigEngine;
use Illuminate\View\Environment;
use Illuminate\Validation\MessageBag;

class ViewManager extends Manager {

	/**
	 * Create a new driver instance.
	 *
	 * @param  string  $driver
	 * @return mixed
	 */
	protected function createDriver($driver)
	{
		$driver = parent::createDriver($driver);

		$driver->share('app', $this->app);

		// If the current session has an "errors" variable bound to it, we will share
		// its value with all view instances so the views can easily access errors
		// without having to bind. An empty bag is set when there aren't errors.
		if ($this->sessionHasErrors())
		{
			$errors = $this->app['session']->get('errors');

			$driver->share('errors', $errors);
		}
		else
		{
			$driver->share('errors', new MessageBag);
		}

		return $driver;
	}

	/**
	 * Create an instance of the PHP view driver.
	 *
	 * @return Illuminate\View\Environment
	 */
	protected function createPhpDriver()
	{
		$paths = $this->app['config']['view.paths'];

		$engine = new PhpEngine($this->app['files'], $paths);

		return new Environment($engine);
	}

	/**
	 * Create an instance of the Twig view driver.
	 *
	 * @return Illuminate\View\Environment
	 */
	protected function createTwigDriver()
	{
		$paths = $this->app['config']['view.paths'];

		$engine = new TwigEngine(new Twig_Environment, $this->app['files'], $paths);

		$engine->getTwig()->setLoader($engine);

		return new Environment($engine);
	}

	/**
	 * Determine if the application session has errors.
	 *
	 * @return bool
	 */
	public function sessionHasErrors()
	{
		return isset($this->app['session']) and $this->app['session']->has('errors');
	}

	/**
	 * Get the default cache driver name.
	 *
	 * @return string
	 */
	protected function getDefaultDriver()
	{
		return $this->app['config']['view.driver'];
	}

}