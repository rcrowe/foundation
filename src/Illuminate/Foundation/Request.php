<?php namespace Illuminate\Foundation;

class Request extends \Symfony\Component\HttpFoundation\Request {

	/**
	 * Determine if the request contains a given input item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return trim((string) $this->get($key)) !== '';
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
		return $this->get($key, $default);
	}

	/**
	 * Retrieve a query string item from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function query($key = null, $default = null)
	{
		return $this->retrieveItem('query', $key, $default);
	}

	/**
	 * Retrieve a cookie from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function cookie($key = null, $default = null)
	{
		return $this->retrieveItem('cookies', $key, $default);
	}

	/**
	 * Retrieve a file from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	public function file($key = null, $default = null)
	{
		return $this->retrieveItem('files', $key, $default);
	}

	/**
	 * Retrieve a header from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function header($key = null, $default = null)
	{
		return $this->retrieveItem('headers', $key, $default);
	}

	/**
	 * Retrieve a parameter item from a given source.
	 *
	 * @param  string  $source
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	protected function retrieveItem($source, $key, $default)
	{
		if (is_null($key))
		{
			return $this->$source->all();
		}
		else
		{
			return $this->$source->get($key, $default);
		}
	}

	/**
	 * Get the JSON payload for the request.
	 *
	 * @return object
	 */
	public function json()
	{
		return json_decode($this->getContent());
	}

	/**
	 * Get the root URL for the application.
	 *
	 * @return string
	 */
	public function getRootUrl()
	{
		return $this->getScheme().'://'.$this->getHttpHost().$this->getBasePath();
	}

}