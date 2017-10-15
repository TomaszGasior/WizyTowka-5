<?php

/**
* WizyTówka 5
* This class contains abstraction needed by classes of content types and defines interface required to implement.
*/
namespace WizyTowka;

abstract class ContentTypeAPI
{
	// Data of website page as stdClass objects. It will be set by Website class.
	protected $_contents;
	protected $_settings;

	// HTML code parts classes. It will be set by WebsiteRenderer class.
	protected $_HTMLHead;
	protected $_HTMLMessage;
	protected $_HTMLTemplate;

	// Instance of ContentType plugin class. It will be set by ContentType itself.
	protected $_contentType;

	final public function __construct(ContentType $myContentTypeInstance)
	{
		$this->_contentType = $myContentTypeInstance;

		$this->_prepare();
	}

	final public function setPageData(\stdClass $contents, \stdClass $settings)
	{
		$this->_contents = $contents;
		$this->_settings = $settings;
	}

	final public function setHTMLParts(HTMLTemplate $template, HTMLHead $head, HTMLMessage $message)
	{
		$className = substr(strrchr(static::class, '\\'), 1);  // "WizyTowka\PlainText\SettingsPage" --> "SettingsPage".

		$this->_HTMLTemplate = $template;
		$this->_HTMLTemplate->setTemplate($className);
		$this->_HTMLTemplate->setTemplatePath($this->_contentType->getPath() . '/templates');

		$this->_HTMLHead = $head;
		$this->_HTMLHead->setAssetsPath($this->_contentType->getURL() . '/assets');

		$this->_HTMLMessage = $message;
	}

	// Equivalent of __construct() method for child classes.
	protected function _prepare() {}

	public function POSTQuery()
	{
		throw ContentTypeAPIException::withoutPOSTQueries(static::class);
	}

	abstract public function HTMLContent();
}

class ContentTypeAPIException extends Exception
{
	static public function withoutPOSTQueries($class)
	{
		return new self('Content type part  ' . $class . ' does not support POST queries.', 1);
	}
}