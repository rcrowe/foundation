<?php namespace Illuminate\Foundation\Managers;

use Illuminate\Cache\ApcStore;
use Illuminate\Support\Manager;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\ApcWrapper;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\RedisStore;

class CacheManager extends Manager {

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
	 * Create an instance of the file cache driver.
	 *
	 * @return Illuminate\Cache\FileStore
	 */
	protected function createFileDriver()
	{
		$path = $this->app['config']['cache.path'];

		return new FileStore($this->app['files'], $path);
	}

	/**
	 * Create an instance of the Memcached cache driver.
	 *
	 * @return Illuminate\Cache\MemcachedStore
	 */
	protected function createMemcachedDriver()
	{
		$servers = $this->app['config']['cache.memcached'];

		$memcached = $this->app['memcached.connector']->connect($servers);

		return new MemcachedStore($memcached, $this->app['config']['cache.prefix']);
	}

	/**
	 * Create an instance of the Redis cache driver.
	 *
	 * @return Illuminate\Cache\RedisStore
	 */
	protected function createRedisDriver()
	{
		$redis = $this->app['redis']->connection();

		return new RedisStore($redis, $this->app['config']['cache.prefix']);
	}

	/**
	 * Get the default cache driver name.
	 *
	 * @return string
	 */
	protected function getDefaultDriver()
	{
		return $this->app['config']['cache.driver'];
	}

}