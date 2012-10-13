<?php namespace Illuminate\Foundation\Managers;

use Illuminate\Support\Manager;
use Illuminate\Session\FileStore;
use Illuminate\Session\CookieStore;
use Illuminate\Session\CacheDrivenStore;

class SessionManager extends Manager {

	/**
	 * Create an instance of the cookie session driver.
	 *
	 * @return Illuminate\Session\CookieStore
	 */
	protected function createCookieDriver()
	{
		return new CookieStore($this->app['cookie']);
	}

	/**
	 * Create an instance of the file session driver.
	 *
	 * @return Illuminate\Session\FileStore
	 */
	protected function createFileDriver()
	{
		$path = $this->app['config']['session.path'];

		return new FileStore($this->app['files'], $path);
	}

	/**
	 * Create an instance of the APC session driver.
	 *
	 * @return Illuminate\Session\CacheDrivenStore
	 */
	protected function createApcDriver()
	{
		return $this->createCacheBased('apc');
	}

	/**
	 * Create an instance of the Memcached session driver.
	 *
	 * @return Illuminate\Session\CacheDrivenStore
	 */
	protected function createMemcachedDriver()
	{
		return $this->createCacheBased('memcached');
	}

	/**
	 * Create an instance of the Redis session driver.
	 *
	 * @return Illuminate\Session\CacheDrivenStore
	 */
	protected function createRedisDriver()
	{
		return $this->createCacheBased('redis');
	}

	/**
	 * Create an instance of the "array" session driver.
	 *
	 * @return Illuminate\Session\CacheDrivenStore
	 */
	protected function createArrayDriver()
	{
		return $this->createCacheBased('array');
	}

	/**
	 * Create an instance of a cache driven driver.
	 *
	 * @return Illuminate\Session\CacheDrivenStore
	 */
	protected function createCacheBased($driver)
	{
		return new CacheDrivenStore($this->app['cache']->driver($driver));
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