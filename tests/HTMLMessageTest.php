<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLMessageTest extends TestCase
{
	public function testSuccess() : void
	{
		$object = new __\HTMLMessage('pageMessage');
		$object->success('Example success message.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="pageMessage success" role="alert">Example success message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testError() : void
	{
		$object = new __\HTMLMessage();
		$object->error('Example error message.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message error" role="alert">Example error message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testInfo() : void
	{
		$object = new __\HTMLMessage();
		$object->information('Example neutral message.');  // information() is an alias of info().

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message info" role="alert">Example neutral message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testArguments() : void
	{
		$object = new __\HTMLMessage();
		$object->success('These files were removed: "%s", "%s".', 'example".jpg', 'example<br>.png');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message success" role="alert">These files were removed: "example&quot;.jpg", "example&lt;br&gt;.png".</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testOverwriting() : void
	{
		$object = new __\HTMLMessage();
		$object->info('Example neutral message.');
		$object->error('Example error message.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<div class="message error" role="alert">Example error message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testDefault() : void
	{
		$object1 = new __\HTMLMessage();
		$object1->error('Example error message.');
		$object1->default('Example default success message.');

		$current  = (string)$object1;
		$expected = <<< 'HTML'
<div class="message error" role="alert">Example error message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);

		$object2 = new __\HTMLMessage();
		$object2->default('Example default success message.');

		$current  = (string)$object2;
		$expected = <<< 'HTML'
<div class="message success" role="alert">Example default success message.</div>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testClear() : void
	{
		$object = new __\HTMLMessage();
		$object->error('Example error message.');
		$object->clear();

		$current = (string)$object;
		$this->assertEmpty($current);
	}
}