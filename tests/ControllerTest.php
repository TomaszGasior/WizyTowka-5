<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class ControllerTest extends TestCase
{
	static private $_exampleControllerClass;

	static public function setUpBeforeClass() : void
	{
		// Example controller extending Controller class.
		self::$_exampleControllerClass = get_class(new class extends __\Controller
		{
			static public function URL($target, array $arguments = []) : ?string
			{
				return $target . strrev($target) . '?' . http_build_query($arguments);
			}
		});
	}

	/**
	* @expectedException     WizyTowka\ControllerException
	* @expectedExceptionCode 1
	*/
	public function testPOSTQuery() : void
	{
		$controller = new self::$_exampleControllerClass;
		$controller->POSTQuery();
	}

	/**
	* @runInSeparateProcess
	*/
	public function testRedirectWithControllerURL() : void
	{
		$controller = new self::$_exampleControllerClass;

		try {
			$this->invokePrivateOn($controller)->_redirect('target', ['one' => '1', 'two' => '2']);
			// _redirect() is protected and it throws exception if it's impossible to set properly HTTP header.
		} catch (__\ControllerException $e) {}

		$current  = $this->getLastHTTPHeader();
		$expected = 'Location: targettegrat?one=1&two=2';
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testRedirectWithFullURL() : void
	{
		$controller = new self::$_exampleControllerClass;

		try {
			$this->invokePrivateOn($controller)->_redirect('http://example.org', ['one' => '1', 'two' => '2']);
			// _redirect() is protected and it throws exception if it's impossible to set properly HTTP header.
		} catch (__\ControllerException $e) {}

		$current  = $this->getLastHTTPHeader();
		$expected = 'Location: http://example.org?one=1&two=2';
		$this->assertEquals($expected, $current);
	}
}