<?php

/**
* WizyTówka 5
* Content type — addon.
*/
namespace WizyTowka;

class ContentType extends Addon
{
	private const API_CLASS = __NAMESPACE__ . '\ContentTypeAPI';

	static protected $_addonsSubdir = 'types';
	static protected $_defaultConfig = [
		'namespace' => '',
		'label'     => 'Untitled content type',
		'contents'  => [],
		'settings'  => [],
	];

	private function _initClass(string $className) : ContentTypeAPI
	{
		// Add namespace of content type to autoloader.
		WT()->autoloader->addNamespace($this->namespace, $this->getPath() . '/classes');

		// Content type's classes must extend ContentTypeAPI class.
		$className = $this->namespace . '\\' . $className;
		if (!is_subclass_of($className, self::API_CLASS)) {
			ContentTypeException::invalidClass($className, $APIClass);
		}

		// Init class instance and return it.
		return new $className($this);
	}

	public function initWebsitePageBox() : ContentTypeAPI
	{
		return $this->_initClass('WebsitePageBox');
	}

	public function initEditorPage() : ContentTypeAPI
	{
		return $this->_initClass('EditorPage');
	}

	public function initSettingsPage() : ContentTypeAPI
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