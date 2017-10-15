<?php

/**
* WizyTówka 5
* This class contains abstraction needed by classes of content types and defines interface required to implement.
*/
namespace WizyTowka;

abstract class ContentTypeAPI
{
	// Data of website page as stdClass objects. It will be set by Website class.
	private $_content;
	private $_settings;

	// HTML code parts classes. It will be set by WebsiteRenderer class.
	private $_HTMLHead;
	private $_HTMLMessage;
	private $_HTMLTemplate;

	// Instance of ContentType plugin class. It will be set by ContentType itself.
	private $_contentType;

	final public function __construct(ContentType $myContentTypeInstance)
	{
		$this->_contentType = $myContentTypeInstance;

		$this->_prepare();
	}

	final public function setPageData(stdClass $content, stdClass $settings)
	{
		$this->_content  = $content;
		$this->_settings = $settings;
	}

	final public function setHTMLParts(HTMLTemplate $template, HTMLHead $head, HTMLMessage $message)
	{
		$className = substr(strrchr(static::class, '\\'), 1);  // "WizyTowka\PlainPage\SettingsPage" --> "SettingsPage".

		$this->_HTMLTemplate = $template;
		$this->_HTMLTemplate->setTemplate($className);
		$this->_HTMLTemplate->setTemplatePath($this->_contentType->getPath() . '/templates');

		$this->_HTMLHead = $head;
		$this->_HTMLHead->setAssetsPath($this->_contentType->getURL() . '/assets');

		$this->_HTMLMessage = $message;
	}

	// Equivalent of __construct() method for child classes.
	protected function _prepare() {}

	public function HTTPHeaders() {}

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