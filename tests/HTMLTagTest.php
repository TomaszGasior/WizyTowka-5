<?php

/**
* WizyTówka 5 — unit test
*/
// Workarounds: HTMLTestCase::assertHTMLEquals().

class HTMLTagTest extends PHPUnit\Framework\HTMLTestCase
{
	static private $_exampleClass;

	static public function setUpBeforeClass()
	{
		self::$_exampleClass = new class() extends WizyTowka\HTMLTag
		{
			public function output()
			{
				$attributes = [
					'type'      => 'text',
					'value'     => '',
					'required'  => true,   // Skip attribute value when it's "true".
					'autofocus' => false,  // Skip attribute with "false" value.
					'class'     => $this->_CSSClass,
				];
				$this->_renderHTMLOpenTag('input', $attributes);
			}
		};
	}

	public function testOutput()
	{
		$object = new self::$_exampleClass;

		$object->setCSSClass('mark-required-field');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<input type="text" value="" required class="mark-required-field">
HTML;
		$this->assertHTMLEquals($expected, $current);
	}
}