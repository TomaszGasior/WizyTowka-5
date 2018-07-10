<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLTagTest extends TestCase
{
	static private $_exampleTagClass;

	static public function setUpBeforeClass() : void
	{
		self::$_exampleTagClass = get_class(new class extends __\HTMLTag
		{
			public function output() : void
			{
				$attributes = [
					'type'      => 'text',
					'value'     => '',
					'required'  => true,   // Attribute value should be skipped when it's "true".
					'autofocus' => false,  // Attribute with "false" value should be skipped.
					'class'     => $this->_CSSClass,   // This property is set by HTMLTag's constructor.
				];
				$this->_renderHTMLOpenTag('input', $attributes);
			}
		});
	}

	public function testOutput() : void
	{
		$object = new self::$_exampleTagClass;

		$object->setCSSClass('mark-required-field');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<input type="text" value="" required class="mark-required-field">
HTML;
		$this->assertHTMLEquals($expected, $current);
	}
}