<?php

/**
* WizyTówka 5
* Abstraction for HTML rendering classes like HTMLHead, HTMLMenu, HTMLFormFields etc.
*/
namespace WizyTowka;

abstract class HTMLTag
{
	protected $_CSSClass = '';

	public function __construct($CSSClass = null)
	{
		$this->setCSSClass($CSSClass);
	}

	public function getCSSClass()
	{
		return $this->_CSSClass;
	}

	public function setCSSClass($CSSClass)
	{
		if ($CSSClass) {
			$this->_CSSClass = (string)$CSSClass;
		}
	}

	public function __toString()
	{
		ob_start();
		$this->output();
		return ob_get_clean();
	}

	abstract public function output();

	protected function _renderHTMLOpenTag($tagName, array $HTMLAttributes = [])
	{
		echo '<', $tagName;
		foreach ($HTMLAttributes as $name => $value) {
			if ($value === false) {
				continue;
			}
			echo ' ', $name;
			if ($value !== true) {
				echo '="', $value, '"';
			}
		}
		echo '>';
	}
}