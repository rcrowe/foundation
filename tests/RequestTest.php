<?php

use Illuminate\Foundation\Request;

class RequestTest extends Illuminate\Foundation\TestCase {

	public function testHasMethod()
	{
		$request = Request::create('/', 'GET', array('name' => 'Taylor'));
		$this->assertTrue($request->has('name'));
		$this->assertFalse($request->has('foo'));
	}


	public function testInputMethod()
	{
		$request = Request::create('/', 'GET', array('name' => 'Taylor'));
		$this->assertEquals('Taylor', $request->input('name'));
		$this->assertEquals('Bob', $request->input('foo', 'Bob'));
	}


	public function testQueryMethod()
	{
		$request = Request::create('/', 'GET', array('name' => 'Taylor'));
		$this->assertEquals('Taylor', $request->query('name'));
		$this->assertEquals('Bob', $request->query('foo', 'Bob'));
	}


	public function testCookieMethod()
	{
		$request = Request::create('/', 'GET', array(), array('name' => 'Taylor'));
		$this->assertEquals('Taylor', $request->cookie('name'));
		$this->assertEquals('Bob', $request->cookie('foo', 'Bob'));
	}


	public function testFileMethod()
	{
		$files = array(
			'foo' => array(
				'size' => 500,
				'name' => 'foo.jpg',
				'tmp_name' => __FILE__,
				'type' => 'blah',
				'error' => null,
			),
		);
		$request = Request::create('/', 'GET', array(), array(), $files);
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\File\UploadedFile', $request->file('foo'));
	}


	public function testHeaderMethod()
	{
		$request = Request::create('/', 'GET', array(), array(), array(), array('HTTP_DO_THIS' => 'foo'));
		$this->assertEquals('foo', $request->header('do-this'));
	}


	public function testJSONMethod()
	{
		$request = Request::create('/', 'GET', array(), array(), array(), array(), json_encode(array('taylor' => 'name')));
		$json = $request->json();
		$this->assertEquals('name', $json->taylor);
	}

}