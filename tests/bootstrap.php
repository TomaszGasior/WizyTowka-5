<?php

/**
* WizyTówka 5
* Bootstrap for unit tests.
*/


// Load config with system constants.
include __DIR__ . '/../code/config.php';

// Init system without controller.
include WizyTowka\SYSTEM_DIR . '/init.php';


// Improved test case class used by all tests.
abstract class TestCase extends PHPUnit\Framework\TestCase
{
	// Assertion of HTML code. Needed in tests of HTML classes like HTMLMenu or HTMLHead.
	protected function assertHTMLEquals($expected, $current, ...$arguments)
	{
		$this->assertXmlStringEqualsXmlString(
			(@\DOMDocument::loadHTML($expected, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
			(@\DOMDocument::loadHTML($current,  LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
			...$arguments
		);
	}

	// This method gives ability to access private/protected methods.
	// In tests code instead of this syntax:       $object->_privateMethod('arg1', 'arg2');
	// use this:           $this->invokePrivateOn($object)->_privateMethod('arg1', 'arg2');
	protected function invokePrivateOn($object)
	{
		return new class($object)
		{
			static private $_object;

			public function __construct($object)
			{
				self::$_object = $object;
			}

			public function __call($function, $arguments)
			{
				($reflection = new ReflectionMethod(self::$_object, $function))->setAccessible(true);
				return $reflection->invokeArgs(self::$_object, $arguments);
			}
		};
	}

	// Get last HTTP header sent by header() function using Xdebug extension.
	// @runInSeparateProcess annotation is required.
	protected function getLastHTTPHeader()
	{
		if (!function_exists('xdebug_get_headers')) {
			exit('Xdebug extension is required.');
		}

		return array_reverse(xdebug_get_headers())[0];
	}

	// Get properties of last HTTP cookie sent by setcookie() function.
	protected function getLastHTTPCookie($property)
	{
		$found = preg_match(
			'/^Set-Cookie: (?<name>[^= ]+)=(?<value>[^ ;]+)(?:; expires=(?<expires>[^;]*))?/i',
			$this->getLastHTTPHeader(), $matches
		);

		if (isset($matches['value'])) {
			$matches['value'] = urldecode($matches['value']);
		}
		if (isset($matches['expires'])) {
			$matches['expires'] = strtotime($matches['expires']);
		}

		if ($found) {
			$_COOKIE[$matches['name']] = $matches['value'];
		}

		return ($found and isset($matches[$property])) ? $matches[$property] : false;
	}
}