<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka
{
	function header($string)
	{
		headers_list($string);
	}
	function headers_list($addNewHeader = null) // Oryginal header_list() does not take arguments.
	{
		static $savedHeaders = [];

		if ($addNewHeader) {
			$savedHeaders[] = $addNewHeader;
		}
		else {
			return $savedHeaders;
		}
	}
	// Dirty hack. It is needed to get HTTP headers in tests. header() and headers_list() function do not work in CLI.
	// Fake funtions placed above override built-in PHP functions in CMS namespace.
}
namespace
{
	class ControllerTest extends PHPUnit\Framework\TestCase
	{
		static private $_exampleClass;

		static public function setUpBeforeClass()
		{
			// Example controller in anonymous class. PHP 7 syntax.
			self::$_exampleClass = new class() extends WizyTowka\Controller
			{
				// public function POSTQuery();
				// This controller does not support POST queries.

				static public function URL($target, array $arguments = [])
				{
					return $target . strrev($target) . '?' . http_build_query($arguments);
				}
			};
		}

		public function testFilterPOSTData()
		{
			$_POST = [
				'field1' => 'example content',
				'field2' => '<strong>example content</strong>',
				'nofilter_field3' => '<strong>example content</strong>',
			];

			$controller = new self::$_exampleClass;
			$controller->filterPOSTData();

			$expected = [
				'field1' => 'example content',
				'field2' => '&lt;strong&gt;example content&lt;/strong&gt;',
				'nofilter_field3' => '<strong>example content</strong>',
				'field3' => '<strong>example content</strong>',  // Automatically created alias of "nofilter_field3".
			];

			$this->assertEquals($expected, $_POST);
		}

		/**
		 * @expectedException     WizyTowka\ControllerException
		 * @expectedExceptionCode 1
		 */
		public function testPOSTQuery()
		{
			$controller = new self::$_exampleClass;
			$controller->POSTQuery();
		}

		// There are tests for Controller::_redirect() method below.
		//  * Reflection is used, because method is private/protected.
		//  * Method sends HTTP headers. It will be fetched by overridden header() and headers_list() functions.
		//  * Method stop executing by exit(), so assertion is placed in shutdown function and tests are ran in separate proccesses.

		/**
		* @runInSeparateProcess
		* @covers    WizyTowka\Controller::URL
		*/
		public function testRedirectWithControllerURL()
		{
			$controller = new self::$_exampleClass;

			$redirectMethod = new ReflectionMethod($controller, '_redirect');
			$redirectMethod->setAccessible(true);

			register_shutdown_function(( function(){
				$expected = 'Location: targettegrat?one=1&two=2';
				$current  = WizyTowka\headers_list()[0];

				$this->assertEquals($expected, $current);
			} )->bindTo($this));

			$redirectMethod->invoke(
				$controller,
				'target', ['one' => '1', 'two' => '2']  // _redirect() arguments here.
			);
		}

		/**
		* @runInSeparateProcess
		*/
		public function testRedirectWithFullURL()
		{
			$controller = new self::$_exampleClass;

			$redirectMethod = new ReflectionMethod($controller, '_redirect');
			$redirectMethod->setAccessible(true);

			register_shutdown_function(( function(){
				$expected = 'Location: http://example.org?one=1&two=2';
				$current  = WizyTowka\headers_list()[0];

				$this->assertEquals($expected, $current);
			} )->bindTo($this));

			$redirectMethod->invoke(
				$controller,
				'http://example.org', ['one' => '1', 'two' => '2']  // _redirect() arguments here.
			);
		}
	}
}