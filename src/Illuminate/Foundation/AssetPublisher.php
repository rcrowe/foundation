<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem;

class AssetPublisher {

	/**
	 * The filesystem instance.
	 *
	 * @var Illuminate\Filesystem
	 */
	protected $files;

	/**
	 * The path where assets should be published.
	 *
	 * @var string
	 */
	protected $publishPath;

	/**
	 * The path where packages are located.
	 *
	 * @var string
	 */
	protected $packagePath;

	/**
	 * Create a new asset publisher instance.
	 *
	 * @param  Illuminate\Filesystem  $files
	 * @param  string  $publishPath
	 * @return void
	 */
	public function __construct(Filesystem $files, $publishPath)
	{
		$this->files = $files;
		$this->publishPath = $publishPath;
	}

	/**
	 * Copy all assets from a given path to the publish path.
	 *
	 * @param  string  $source
	 * @param  string  $name
	 * @return bool
	 */
	public function publish($source, $name)
	{
		$destination = $this->publishPath."/packages/{$name}";

		$success = $this->files->copyDirectory($source, $destination);

		// If were unable to publish the assets, it coule mean that the source folder
		// does not exists. So, the developer should probably check that the given
		// source location is valid, otherwise, verify the target's permissions.
		if ( ! $success)
		{
			throw new \RuntimeException("Unable to publish assets.");
		}

		return $success;
	}

	/**
	 * Publish a given package's assets to the publish path.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return bool
	 */
	public function publishPackage($package, $packagePath = null)
	{
		$packagePath = $packagePath ?: $this->packagePath;

		// Once we have the package path we can just create the source and destination
		// path and copy the directory from one to the other. The directory copy is
		// recursive so all nested files and directories will get copied as well.
		$source = $packagePath."/{$package}/public";

		return $this->publish($source, $package);
	}

	/**
	 * Set the default package path.
	 *
	 * @param  string  $packagePath
	 * @return void
	 */
	public function setPackagePath($packagePath)
	{
		$this->packagePath = $packagePath;
	}

}