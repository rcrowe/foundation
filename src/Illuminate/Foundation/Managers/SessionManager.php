<?php namespace Illuminate\Foundation\Managers;

use Illuminate\Session\FileStore;
use Illuminate\Session\CookieStore;

class SessionManager extends Manager {

	/**
	 * Create an instance of the cookie session driver.
	 *
	 * @return Illuminate\Session\CookieStore
	 */
	protected function createCookieDriver()
	{
		return new CookieStore($this->app['encrypter'], $this->app['cookie']);
	}

	/**
	 * Create an instance of the file session driver.
	 *
	 * @return Illuminate\Session\FileStore
	 */
	protected function createFileDriver()
	{
		return new FileStore($this->app['cache']->driver('file'));
	}

	/**
	 * Get the default session driver name.
	 *
	 * @return string
	 */
	protected function getDefaultDriver()
	{
		return $this->app['config']['session.driver'];
	}

}