<?php namespace Illuminate\Foundation;

use Closure;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionHandler {

	/**
	 * Handle the given exception.
	 *
	 * @param  Exception  $exception
	 * @param  array  $handlers
	 * @return void
	 */
	public function handleException($exception, array $handlers)
	{
		foreach ($this->errorHandlers as $handler)
		{
			// If this exception handler does not handle the given exception, we will
			// just go the next one. A Handler may type-hint the exception that it
			// will handle, allowing for more granularity on the error handling.
			if ( ! $this->handlesException($handler, $exception))
			{
				continue;
			}

			if ($exception instanceof HttpExceptionInterface)
			{
				$code = $exception->getStatusCode();
			}
			else
			{
				$code = 500;
			}

			// If the handler returns a non-null response, we will return it so it
			// may get sent back to the browser. Once a handler returns a valid
			// response we will stop iterating or calling the other handlers.
			$response = $handler($exception, $code);

			if ( ! is_null($response))
			{
				return $response;
			}
		}
	}

	/**
	 * Determine if the given handler handles this exception.
	 *
	 * @param  Closure    $handler
	 * @param  Exception  $exception
	 * @return bool
	 */
	protected function handlesException(Closure $handler, $exception)
	{
		$reflection = new ReflectionMethod($handler);

		return $reflection->getNumberOfParameters() > 0 or $this->hints($reflection, $exception);
	}

	/**
	 * Determine if the given handler type hints the exception.
	 *
	 * @param  ReflectionMethod  $reflection
	 * @param  Exception  $exception
	 * @return bool
	 */
	protected function hints(ReflectionMethod $reflection, $exception)
	{
		$parameters = $reflection->getParameters();

		$expected = $parameters[0];

		return $expected->getClass() and $expected->getClass()->isInstance($exception);
	}

}