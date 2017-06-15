<?php

/**
* WizyTówka 5 — workarounds for unit tests
*/

// Extented PHPUnit's TestCase class with additional assertHTMLEquals().
// It is useful in unit test of HTML classes like HTMLMenuTest or HTMLHeadTest.
namespace PHPUnit\Framework
{
	abstract class HTMLTestCase extends TestCase
	{
		protected function assertHTMLEquals($expected, $current, $message = null)
		{
			$this->assertXmlStringEqualsXmlString(
				(@\DOMDocument::loadHTML($expected, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
				(@\DOMDocument::loadHTML($current,  LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
				$message
			);
		}
	}
}

// Built-in PHP functions overwritten in CMS namespace.
// These functions are needed by some CMS components but do not work in command line interface.
namespace WizyTowka
{
	// $_SERVER values needed by SessionManager trait, not defined in CLI.
	$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
	$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0';

	// Implementation of setcookie() function for test of SessionManager trait. setcookie() does nothing in CLI.
	function setcookie($name, $value, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
	{
		if ($expire > time()) {
			$_COOKIE[$name] = $value;
		}
		else {
			unset($_COOKIE[$name]);
		}

		\workaroundsData()->lastCookie = compact('name', 'value', 'expire', 'path', 'domain', 'secure', 'httponly');
	}

	// Implementation of header() function for test of Controller class. header() does nothing in CLI.
	function header($string)
	{
		\workaroundsData()->lastHeader = $string;
	}
}

// Additional workaroundsData() function. It keeps data from overwritten PHP functions for unit tests.
namespace
{
	function workaroundsData()
	{
		static $data;
		return ($data) ? $data : ($data = new \stdClass);
	}
}