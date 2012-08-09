<?php namespace Illuminate\Foundation\Managers;

use Illuminate\Cache\ApcStore;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\ApcWrapper;
use Illuminate\Cache\ArrayStore;

class CacheManager extends Manager {

	/**
	 * Get a cache driver instance.
	 *
	 * @param  string  $driver
	 * @return Illuminate\Cache\Store
	 */
	public function driver($driver = null)
	{
		$driver = $driver ?: $this->getDefaultDriver();

		// If the given driver has not been created before, we will create the instance
		// here and cache it so we can return it next time very quickly. If their is
		// already a driver created by this name, we'll just return that instance.
		if ( ! isset($this->drivers[$driver]))
		{
			$this->drivers[$driver] = $this->createDriver($driver);
		}

		return $this->drivers[$driver];
	}

	/**
	 * Create an instance of the APC cache driver.
	 *
	 * @return Illuminate\Cache\ApcStore
	 */
	protected function createApcDriver()
	{
		return new ApcStore(new ApcWrapper);
	}

	/**
	 * Create an instance of the array cache driver.
	 *
	 * @return Illuminate\Cache\ArrayStore
	 */
	protected function createArrayDriver()
	{
		return new ArrayStore;
	}

	/**
	 * Create an instance of the array cache driver.
	 *
	 * @return Illuminate\Cache\ArrayStore
	 */
	protected function createFileDriver()
	{
		return new FileStore($this->app['files'], $this->app['cache.path']);
	}

	/**
	 * Create an instance of the array cache driver.
	 *
	 * @return Illuminate\Cache\ArrayStore
	 */
	protected function createMemcachedDriver()
	{
		$config = $this->app['cache.memcached'];

		$memcached = $this->app['memcached.connector']->connect($config);

		return new MemcachedStore($memcached, $this->app['cache.prefix']);
	}

	/**
	 * Get the default cache driver name.
	 *
	 * @return string
	 */
	protected function getDefaultDriver()
	{
		return $this->app['cache.driver'];
	}

}