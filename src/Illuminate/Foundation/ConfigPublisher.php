<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem;

class ConfigPublisher {

	/**
	 * The filesystem instance.
	 *
	 * @var Illuminate\Filesystem
	 */
	protected $files;

	/**
	 * The destination of the config files.
	 *
	 * @var string
	 */
	protected $publishPath;

	/**
	 * The path to the application's packages.
	 *
	 * @var string
	 */
	protected $packagePath;

	/**
	 * Create a new configuration publisher instance.
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
	 * Publish the configuration files for a package.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return void
	 */
	public function publishPackage($package, $packagePath = null)
	{
		$packagePath = $packagePath ?: $this->packagePath;

		// First we will figure out the source of the package's configuration location
		// which we do by convention. Once we have that we will move the files over
		// to the "main" configuration directory for this particular application.
		$source = $packagePath."/{$package}/src/config";

		$destination = $this->publishPath."/packages/{$package}";

		return $this->files->copyDirectory($source, $destination);
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