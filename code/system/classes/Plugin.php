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

	public function init() : void
	{
		// Add plugin's namespace to autoloader.
		WT()->autoloader->addNamespace($this->namespace, $this->getPath() . '/classes');

		// Init plugin by specified callback.
		($this->namespace . '\\' . $this->init)($this);
	}
}