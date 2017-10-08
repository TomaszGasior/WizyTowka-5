<?php

/**
* WizyTówka 5
* Example plugin.
*/
namespace WizyTowka\LoadingTime;

class Plugin
{
	static public function init()
	{
		new self;
	}

	private function __construct()
	{
		register_shutdown_function([$this, '_output']);
	}

	public function _output()
	{
		echo PHP_EOL, '<!-- loading time: ', microtime(1)-$_SERVER['REQUEST_TIME_FLOAT'], ' -->';
	}
}