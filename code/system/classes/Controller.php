<?php

/**
* WizyTówka 5
* Abstract controller.
*/
namespace WizyTowka;

abstract class Controller
{
	public function filterPOSTData()
	{
		$HTMLFilter = function(array &$array) use (&$HTMLFilter)
		{
			$aliases = [];
			foreach ($array as $key => &$value) {
				$key = explode('_', $key, 2);
				if ($key[0] != 'nofilter') {
					is_array($value) ? $HTMLFilter($value) : $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
				}
				elseif (!empty($key[1])) {
					$aliases[$key[1]] =& $value;
				}
			}
			$array += $aliases;
			// $_POST elements with "nofilter_" prefix will not be filtered.
			// For these elements referenced aliases without "nofilter_" prefix will be created.
		};

		$HTMLFilter($_POST);
	}

	public function POSTQuery()
	{
		throw ControllerException::withoutPOSTQueries(static::class);
	}

	public function output() {}

	protected function _redirect($target, array $arguments = [])
	{
		$url = (strpos($target, '/') === false and strpos($target, '?') === false)
			 ? static::URL($target, $arguments)
			 : ($target . ($arguments ? '?'.http_build_query($arguments) : ''));

		header('Location: ' . $url);
		exit;
	}

	// This method should return fully qualified name of controller class according to URL.
	static public function getControllerClass()
	{
		return static::class;
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
}