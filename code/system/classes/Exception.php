<?php

/**
* WizyTówka 5
* Content management system own exception.
*/
namespace WizyTowka;

class Exception extends \Exception
{
	public function __construct()
	{
		if (func_num_args() < 2) {
			throw new \Exception('Exception must have code.');
		}

		call_user_func_array([get_parent_class($this), '__construct'], func_get_args());
	}
}