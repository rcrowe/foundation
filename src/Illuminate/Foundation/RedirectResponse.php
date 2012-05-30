<?php namespace Illuminate\Foundation;

use Illuminate\Session\Store as SessionStore;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse {

	/**
	 * The session store implementation.
	 *
	 * @var Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Write a piece of data to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Illuminate\Foundation\RedirectResponse
	 */
	public function with($key, $value)
	{
		$this->session->put($key, $value);

		return $this;
	}

	/**
	 * Get the session store implementation.
	 *
	 * @return Illuminate\Session\Store
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set the session store implementation.
	 *
	 * @param  Illuminate\Session\Store  $store
	 * @return void
	 */
	public function setSession(SessionStore $session)
	{
		$this->session = $session;
	}

}