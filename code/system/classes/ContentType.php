<?php

/**
* WizyTówka 5
* Content type — addon.
*/
namespace WizyTowka;

class ContentType extends Addon
{
	static protected $_addonsSubdir = 'types';
	static protected $_defaultConfig = [
		'namespace' => '',
		'label'     => 'Untitled content type',
		'contents'  => [],
		'settings'  => [],
	];

	private function _initClass($className)
	{
		// Add namespace of content type to autoloader.
		if (!Autoloader::namespaceExists($this->namespace)) {
			Autoloader::addNamespace($this->namespace, $this->getPath().'/classes');
		}

		// Content type's classes must extend ContentTypeAPI class.
		$className = $this->namespace . '\\' . $className;
		$APIClass  = __NAMESPACE__ . '\ContentTypeAPI';
		if (!is_subclass_of($className, $APIClass)) {
			ContentTypeException::invalidClass($className, $APIClass);
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
	static public function invalidClass($invalidClass, $APIClass)
	{
		return new self('Content type class ' . $invalidClass . ' must extend ' . $APIClass . 'class.', 1);
	}
}