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
		return trim((string) $this->input($key)) !== '';
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
		$input = $this->request->all();

		// If the key is null, we'll merge the request input with the query string
		// and return the entire array. This makes it convenient to get all of
		// the input for the entire Request from both of the input sources.
		if (is_null($key))
		{
			return array_merge($input, $this->query());
		}

		$value = isset($input[$key]) ? $input[$key] : null;

		// If the value is null, we'll try to pull it from the query values then
		// return the default value if it doesn't exist. This allows query to
		// fallback in place of the input for the current request instance.
		if (is_null($value))
		{
			return $this->query($key, $default);
		}

		return $value;
	}

	/**
	 * Get a subset of the items from the input data.
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public function only($keys)
	{
		return array_intersect_key($this->input(), array_flip((array) $keys));
	}

	/**
	 * Get all of the input except for a specified array of items.
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public function except($keys)
	{
		return array_diff_key($this->input(), array_flip((array) $keys));
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