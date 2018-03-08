<?php

/**
* WizyTówka 5
* Theme — addon.
*/
namespace WizyTowka;

class Theme extends Addon
{
	static protected $_addonsSubdir = 'themes';
	static protected $_defaultConfig = [
		'label'      => 'Untitled theme',
		'minified'   => false,
		'responsive' => false,
		'templates'  => [],
	];
}