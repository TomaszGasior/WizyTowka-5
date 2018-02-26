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

	public function testFilterPOSTData()
	{
		$_POST = [
			'field1' => 'example content',
			'field2' => '<strong>example content</strong>',
			'nofilter_field3' => '<strong>example content</strong>',
			'array1' => [
				'field4' => 'example content',
				'field5' => '<strong>example content</strong>',
				'nofilter_field6' => '<strong>example content</strong>',
				'array2' => [
					'field7' => 'example content',
					'field8' => '<strong>example content</strong>',
					'nofilter_field9' => '<strong>example content</strong>',
				],
			],
		];

		$controller = new self::$_exampleController;
		$controller->filterPOSTData();    // filterPOSTData() changes $_POST array directly.

		$expected = [
			'field1' => 'example content',
			'field2' => '&lt;strong&gt;example content&lt;/strong&gt;',
			'nofilter_field3' => '<strong>example content</strong>',
			'array1' => [
				'field4' => 'example content',
				'field5' => '&lt;strong&gt;example content&lt;/strong&gt;',
				'nofilter_field6' => '<strong>example content</strong>',
				'array2' => [
					'field7' => 'example content',
					'field8' => '&lt;strong&gt;example content&lt;/strong&gt;',
					'nofilter_field9' => '<strong>example content</strong>',
				],
			],
		];
		$this->assertEquals($expected, $_POST);
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