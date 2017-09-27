<?php

/**
* WizyTówka 5 — unit test
*/
class TextTest extends PHPUnit\Framework\TestCase
{
	public function testGetChar()
	{
		$textObject = new WizyTowka\Text('Zażółć gęślą jaźń');

		$current  = $textObject->getChar(3);
		$expected = 'ó';
		$this->assertEquals($expected, $current);
	}

	public function testGetLength()
	{
		$textObject = new WizyTowka\Text('Zażółć gęślą jaźń');

		$current  = $textObject->getLength();
		$expected = 17;
		$this->assertEquals($expected, $current);
	}

	public function testLowercase()
	{
		$textObject = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject->lowercase();

		$current  = $textObject->get();
		$expected = 'zażółć gęślą jaźń';
		$this->assertEquals($expected, $current);
	}

	public function testUppercase()
	{
		$textObject = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject->uppercase();

		$current  = $textObject->get();
		$expected = 'ZAŻÓŁĆ GĘŚLĄ JAŹŃ';
		$this->assertEquals($expected, $current);
	}

	public function testCut()
	{
		$textObject1 = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject1->cut(5);

		$current  = $textObject1->get();
		$expected = 'Zażół';
		$this->assertEquals($expected, $current);

		$textObject2 = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject2->cut(-6);

		$current  = $textObject2->get();
		$expected = 'ą jaźń';
		$this->assertEquals($expected, $current);
	}

	public function testCorrectTypography()
	{
		// Examples sources: https://pl.wikipedia.org, https://sjp.pwn.pl, https://encyklopedia.pwn.pl.
		$exampleCodeBefore = <<< TEXT
<h2 class="title">"A oto przykładowy tekst"</h2>
<p>Nie ma się nad czym zastanawiać, "koń jaki jest, każdy widzi".</p>
<p>Kandydat na posła potrafił tylko <code style="color: blue">powtarzać za Gierkiem "Pomożecie?"</code>.</p>
<pre>Czy można jeszcze wątpić, że "tak naprawdę nie dzieje się nic i nie stanie się nic aż do końca"?</pre>
<dl>
	<dt>alfabet Morse'a</dt><dd>alfabet telegraficzny, w którym znaki graficzne (litery, cyfry i in.) są przedstawione w postaci kombinacji kropek i kresek</dd>
	<dt>cappuccino</dt><dd>czarna kawa z dodatkiem zmiksowanego mleka; też: porcja tego napoju</dd>
</dl>
TEXT;
		$exampleCodeAfter = <<< TEXT
<h2 class="title">„A oto przykładowy tekst”</h2>
<p>Nie ma się nad czym zastanawiać, „koń jaki jest, każdy widzi”.</p>
<p>Kandydat na posła potrafił tylko <code style="color: blue">powtarzać za Gierkiem "Pomożecie?"</code>.</p>
<pre>Czy można jeszcze wątpić, że "tak naprawdę nie dzieje się nic i nie stanie się nic aż do końca"?</pre>
<dl>
	<dt>alfabet Morse’a</dt><dd>alfabet telegraficzny, w\u{00A0}którym znaki graficzne (litery, cyfry i\u{00A0}in.) są przedstawione w\u{00A0}postaci kombinacji kropek i\u{00A0}kresek</dd>
	<dt>cappuccino</dt><dd>czarna kawa z\u{00A0}dodatkiem zmiksowanego mleka; też: porcja tego napoju</dd>
</dl>
TEXT;

		$textObject = new WizyTowka\Text($exampleCodeBefore);
		$textObject->correctTypography();

		$current  = $textObject->get();
		$expected = $exampleCodeAfter;
		$this->assertEquals($expected, $current);
	}

	public function testMakeFragment()
	{
		$textObject1 = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject1->makeFragment(12, '…');

		$current  = $textObject1->get();
		$expected = 'Zażółć gęślą…';
		$this->assertEquals($expected, $current);

		$textObject2 = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject2->makeFragment(15, '…');

		$current  = $textObject2->get();
		$expected = 'Zażółć gęślą…';  // Broken word should be removed.
		$this->assertEquals($expected, $current);
	}

	public function testMakeMiddleFragment()
	{
		$textObject = new WizyTowka\Text('Zażółć gęślą jaźń');
		$textObject->makeMiddleFragment(12, ' (...) ');

		$current  = $textObject->get();
		$expected = 'Zażółć (...) jaźń';
		$this->assertEquals($expected, $current);
	}

	public function testMakeSlug()
	{
		$textObject = new WizyTowka\Text('Zażółć  gęślą _ jaźń');
		$textObject->makeSlug();

		$current  = $textObject->get();
		$expected = 'zazolc-gesla-_-jazn';
		$this->assertEquals($expected, $current);
	}

	public function testFormatAsDateTime()
	{
		$textObject1 = new WizyTowka\Text('1997-06-03 16:30');
		$textObject1->formatAsDateTime('%R %d.%m.%Y');

		$current  = $textObject1->get();
		$expected = '16:30 03.06.1997';
		$this->assertEquals($expected, $current);

		$unixTimestamp = time();
		$textObject2 = new WizyTowka\Text($unixTimestamp);
		$textObject2->formatAsDateTime('%Y-%m-%d');

		$current  = $textObject2->get();
		$expected = strftime('%Y-%m-%d', $unixTimestamp);
		$this->assertEquals($expected, $current);
	}
}