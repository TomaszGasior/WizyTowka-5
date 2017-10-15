<?php

/**
* WizyTówka 5
* Content type — addon.
*/
namespace WizyTowka;

class ContentType extends Addon
{
	static protected $_addonsSubdir = 'types';

	private function _initClass($className)
	{
		static $autoloaderConfigured = false;

		// Add namespace of content type to autoloader.
		if (!$autoloaderConfigured) {
			Autoloader::addNamespace($this->namespace, $this->getPath().'/classes');
			$autoloaderConfigured = true;
		}

		// Content type's classes must extend ContentTypeAPI class.
		$className = $this->namespace . '\\' . $className;
		$APIClass  = __NAMESPACE__ . '\ContentTypeAPI';
		if (!is_subclass_of($className, $APIClass)) {
			ContentTypeException::invalidClass($this->_name, $className, $APIClass);
		}

		// Init class instance and return it.
		return new $className($this);
	}

	public function initWebsitePageBox()
	{
		return $this->_initClass('WebsitePageBox');
	}

	public function initEditorPage()
	{
		return $this->_initClass('EditorPage');
	}

	public function initSettingsPage()
	{
		return $this->_initClass('SettingsPage');
	}
}

class ContentTypeException extends Exception
{
	static public function invalidClass($name, $invalidClass, $APIClass)
	{
		return new self('Class ' . $invalidClass . ' of content type "' . $name . '" must extend ' . $APIClass . 'class.', 1);
	}
}