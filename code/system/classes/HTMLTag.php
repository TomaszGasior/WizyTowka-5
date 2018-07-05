<?php

/**
* WizyTówka 5
* Abstraction for HTML rendering classes like HTMLHead, HTMLMenu, HTMLFormFields etc.
*/
namespace WizyTowka;

abstract class HTMLTag
{
	protected $_CSSClass = '';

	public function __construct(string $CSSClass = null)
	{
		if ($CSSClass) {
			$this->setCSSClass($CSSClass);
		}
	}

	public function getCSSClass() : ?string
	{
		return $this->_CSSClass;
	}

	public function setCSSClass(?string $CSSClass) : void
	{
		$this->_CSSClass = $CSSClass;
	}

	public function __toString() : string
	{
		ob_start();
		$this->output();
		return ob_get_clean();
	}

	abstract public function output() : void;

	protected function _renderHTMLOpenTag(string $tagName, array $HTMLAttributes = []) : void
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