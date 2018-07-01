<?php

/**
* WizyTówka 5
* Plugin — addon.
*/
namespace WizyTowka;

class Plugin extends Addon
{
	static protected $_addonsSubdir = 'plugins';
	static protected $_defaultConfig = [
		'namespace' => '',
		'init'      => '',
	];

	public function init()
	{
		// Add plugin's namespace to autoloader.
		WT()->autoloader->addNamespace($this->namespace, $this->getPath() . '/classes');

		// Init plugin by specified callback.
		call_user_func($this->namespace . '\\' . $this->init, $this);
		// Syntax like ($this->namespace . '\\' . $this->init)($this) cannot be used
		// because of backwards compatibility with PHP 5.6.
	}
}