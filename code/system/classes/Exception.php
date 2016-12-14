<?php

/**
* WizyTówka 5
* Content management system own exception.
*/
namespace WizyTowka;

class Exception extends \Exception
{
	public function __construct(...$arguments)
	{
		if (count($arguments) < 2) {
			throw new \Exception('Exception must have code.');
		}

		call_user_func_array([parent::class, '__construct'], $arguments);
	}
}