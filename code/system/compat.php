<?php

/**
* WizyTówka 5
* Workarounds for backwards compatibility with PHP 5.6.
*/


namespace
{
	// Constant added in PHP 7.
	// More here: http://php.net/manual/en/reserved.constants.php#constant.php-int-min
	const PHP_INT_MIN = ~PHP_INT_MAX;

	// Poor implementation of random_int() function, which was added in PHP 7.
	function random_int($min, $max)
	{
		if ($min != PHP_INT_MIN or $max != PHP_INT_MAX) {
			trigger_error('random_int() poor implementation.', E_USER_WARNING);
		}

		if (function_exists('openssl_random_pseudo_bytes')
			and $random = hexdec(bin2hex(@openssl_random_pseudo_bytes(5)))
			and is_int($random)) {
			return $random;
		}

		return mt_rand(-mt_getrandmax(), mt_getrandmax());
	}
}

namespace WizyTowka
{
	// Overwrite array_column() function from global namespace to add ability
	// to using objects in $input parameter. This feature was added in PHP 7.
	// More here: http://php.net/manual/en/function.array-column.php#refsect1-function.array-column-changelog
	function array_column($input, $column_key, $index_key = null)
	{
		if (!is_object(array_values($input)[0])) {
			return \array_column(...func_get_args());
		}

		$array = [];
		foreach ($input as $object) {
			if ($index_key) {
				$array[$object->$index_key] = $object->$column_key;
			}
			else {
				$array[] = $object->$column_key;
			}
		}
		return $array;
	}
}

namespace WizyTowka\AdminPages
{
	function array_column(...$arguments)
	{
		return \WizyTowka\array_column(...$arguments);
	}
}