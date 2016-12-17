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
		$HTMLFilter = function(&$value, $key) use (&$HTMLFilter) {
			// Values with key prefixed by "nofilter_" will not be filtered.
			if (substr($key, 0, 9) != 'nofilter_') {
				is_array($value) ? array_walk($value, $HTMLFilter) : $value = htmlspecialchars($value);
			}
		};

		$HTMLFilter($_POST, null);
	}

	public function POSTQuery()
	{
		throw new Exception('This controller does not support POST queries.', 21);
	}

	public function output()
	{
		return;
	}

	protected function _redirect($target, $arguments = [])
	{
		$url = (strpos($target, '/') === false and strpos($target, '?') === false) ? static::URL($target, $arguments)
			 : ($target . ($arguments ? '?'.http_build_query($arguments) : ''));

		header('Location: ' . $url);
		exit;
	}

	/*abstract*/ static public function URL($target, $arguments = []) {}
	// This method should return URL to specified target (page of site or page of admin panel).
	// It should be abstract, but is not because of backward compatibility with PHP 5.6.
	// More informations: http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.error-handling.strict
}