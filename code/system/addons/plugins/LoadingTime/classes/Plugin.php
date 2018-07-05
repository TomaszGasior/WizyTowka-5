<?php

/**
* WizyTówka 5
* Example plugin.
*/
namespace WizyTowka\LoadingTime;
use WizyTowka as __;

class Plugin
{
	static public function init() : void
	{
		__\WT()->hooks->addAction('End', [__CLASS__ , 'output']);
	}

	static public function output() : void
	{
		echo PHP_EOL, '<!-- loading time: ', microtime(1) - $_SERVER['REQUEST_TIME_FLOAT'], ' -->';
	}
}