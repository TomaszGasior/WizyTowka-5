<?php

/**
* WizyTówka 5 — unit test
*/
class ControllerTest extends TestCase
{
	static private $_exampleController;

	static public function setUpBeforeClass()
	{
		// Example controller in anonymous class. PHP 7 syntax.
		self::$_exampleController = get_class(new class() extends WizyTowka\Controller
		{
			// public function POSTQuery() {} // This controller does not support POST queries.

			static public function URL($target, array $arguments = [])
			{
				return $target . strrev($target) . '?' . http_build_query($arguments);
			}
		});
	}

	/**
	 * @expectedException     WizyTowka\ControllerException
	 * @expectedExceptionCode 1
	 */
	public function testPOSTQuery()
	{
		$controller = new self::$_exampleController;
		$controller->POSTQuery();
	}

	/**
	* @runInSeparateProcess
	*/
	public function testRedirectWithControllerURL()
	{
		$controller = new self::$_exampleController;

		try {
			$this->invokePrivateOn($controller)->_redirect('target', ['one' => '1', 'two' => '2']);
			// _redirect() is protected and it throws exception if it's impossible to set properly HTTP header.
		} catch (WizyTowka\ControllerException $e) {}

		$current  = $this->getLastHTTPHeader();
		$expected = 'Location: targettegrat?one=1&two=2';
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testRedirectWithFullURL()
	{
		$controller = new self::$_exampleController;

		try {
			$this->invokePrivateOn($controller)->_redirect('http://example.org', ['one' => '1', 'two' => '2']);
			// _redirect() is protected and it throws exception if it's impossible to set properly HTTP header.
		} catch (WizyTowka\ControllerException $e) {}

		$current  = $this->getLastHTTPHeader();
		$expected = 'Location: http://example.org?one=1&two=2';
		$this->assertEquals($expected, $current);
	}
}