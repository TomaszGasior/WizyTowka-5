<?php

/**
* WizyTówka 5
* Abstract controller.
*/
namespace WizyTowka;

abstract class Controller
{
	public function POSTQuery()
	{
		throw ControllerException::withoutPOSTQueries(static::class);
	}

	public function output() {}

	protected function _redirect($target, array $arguments = [])
	{
		$url = (strpos($target, '/') === false and strpos($target, '?') === false)
			 ? static::URL($target, $arguments)
			 : ($target . ($arguments ? '?' . http_build_query($arguments) : ''));

		@header('Location: ' . $url);

		in_array('Location: ' . $url, headers_list()) and exit();
		throw ControllerException::unsuccessfulHeader($url);
	}

	// This method should return URL to specified target (page of site or page of admin panel).
	// It should be abstract but is not because of backward compatibility with PHP 5.6.
	// More here: http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.error-handling.strict
	/*abstract*/ static public function URL($target, array $arguments = []) {}
}

class ControllerException extends Exception
{
	static public function withoutPOSTQueries($class)
	{
		return new self('Controller ' . $class . ' does not support POST queries.', 1);
	}
	static public function unallowedKeyInURLArgument($unallowedKey)
	{
		return new self('Argument of URL must not have key named "' . $unallowedKey . '".', 2);  // Exception used by child classes.
	}
	static public function unsuccessfulHeader($url)
	{
		return new self('Unsuccessful redirection by HTTP header to URL: "' . $url . '".', 3);
	}
}