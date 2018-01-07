<?php

/**
* WizyTówka 5
* Plugin — addon.
*/
namespace WizyTowka;

class Plugin extends Addon
{
	static protected $_addonsSubdir = 'plugins';

	protected $_defaultConfig = [
		'namespace' => '',
		'init'      => '',
	];

	public function init()
	{
		// Add plugin's namespace to autoloader.
		Autoloader::addNamespace($this->namespace, $this->getPath().'/classes');

		// Init plugin by specified callback.
		call_user_func($this->init, $this);
		// Syntax like ($this->init)($this) cannot be used because of backwards compatibility with PHP 5.6.
	}
}