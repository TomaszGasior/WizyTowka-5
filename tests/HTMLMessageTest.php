<?php

/**
* WizyTówka 5 — unit test
*/
class HTMLMessageTest extends TestCase
{
	public function testSuccess()
	{
		$object = new WizyTowka\HTMLMessage('pageMessage');
		$object->success('Example success message.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="pageMessage success" role="alert">Example success message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testError()
	{
		$object = new WizyTowka\HTMLMessage();
		$object->error('Example error message.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message error" role="alert">Example error message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testInfo()
	{
		$object = new WizyTowka\HTMLMessage();
		$object->information('Example neutral message.');  // information() is an alias of info().

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message info" role="alert">Example neutral message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testOverwriting()
	{
		$object = new WizyTowka\HTMLMessage();
		$object->information('Example neutral message.');
		$object->error('Example error message.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message error" role="alert">Example error message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testDefault()
	{
		$object1 = new WizyTowka\HTMLMessage();
		$object1->error('Example error message.');
		$object1->default('Example default success message.');

		$current  = (string)$object1;
		$expected = <<< 'HTML'
<div class="message error" role="alert">Example error message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);

		$object2 = new WizyTowka\HTMLMessage();
		$object2->default('Example default success message.');

		$current  = (string)$object2;
		$expected = <<< 'HTML'
<div class="message success" role="alert">Example default success message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testClear()
	{
		$object = new WizyTowka\HTMLMessage();
		$object->error('Example error message.');
		$object->clear();

		$current = (string)$object;
		$this->assertEmpty($current);
	}
}