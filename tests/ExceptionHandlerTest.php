<?php

use Illuminate\Foundation\ExceptionHandler;

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase {

	public function testExceptionHandlerReturnsNullWhenNoHandlersHandleGivenException()
	{
		$handler = new ExceptionHandler;
		$exception = new InvalidArgumentException;
		$callback = function(RuntimeException $e) {};
		$this->assertNull($handler->handle($exception, array($callback)));
	}


	public function testExceptionHandlerReturnsResponseWhenHandlerFound()
	{
		$handler = new ExceptionHandler;
		$exception = new RuntimeException;
		$callback = function(RuntimeException $e) { return 'foo'; };
		$this->assertEquals('foo', $handler->handle($exception, array($callback)));
	}


	public function testGlobalHandlersAreCalled()
	{
		$handler = new ExceptionHandler;
		$exception = new RuntimeException;
		$callback = function($e) { return 'foo'; };
		$this->assertEquals('foo', $handler->handle($exception, array($callback)));
	}


	public function testAllHandlersAreCalled()
	{
		$_SERVER['__exception.handler'] = 0;
		$handler = new ExceptionHandler;
		$exception = new RuntimeException;
		$callback1 = function($e) { $_SERVER['__exception.handler']++; };
		$callback2 = function($e) { $_SERVER['__exception.handler']++; };
		$handler->handle($exception, array($callback1, $callback2));
		unset($_SERVER['__exception.handler']);
	}


	public function testFiveHundredCodeGivenOnNormalExceptions()
	{
		$handler = new ExceptionHandler;
		$exception = new RuntimeException;
		$callback = function($e, $code) { return $code; };
		$this->assertEquals(500, $handler->handle($exception, array($callback)));	
	}


	public function testHttpStatusCodeGivenOnHttpExceptions()
	{
		$handler = new ExceptionHandler;
		$exception = new Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
		$callback = function($e, $code) { return $code; };
		$this->assertEquals(404, $handler->handle($exception, array($callback)));	
	}

}