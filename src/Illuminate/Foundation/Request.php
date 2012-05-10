<?php namespace Illuminate\Foundation;

class Request extends \Symfony\Component\HttpFoundation\Request {

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