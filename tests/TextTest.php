<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class TextTest extends TestCase
{
	public function testGetChar() : void
	{
		$textObject = new __\Text('Zażółć gęślą jaźń');

		$current  = $textObject->getChar(3);
		$expected = 'ó';
		$this->assertEquals($expected, $current);

		$current  = $textObject->getChar(-6);
		$expected = 'ą';
		$this->assertEquals($expected, $current);

		$this->assertNull($textObject->getChar(17));
		$this->assertNull($textObject->getChar(-18));
	}

	public function testGetLength() : void
	{
		$textObject = new __\Text('Zażółć gęślą jaźń');

		$current  = $textObject->getLength();
		$expected = 17;
		$this->assertEquals($expected, $current);
	}

	public function testLowercase() : void
	{
		$textObject = new __\Text('Zażółć gęślą jaźń');
		$textObject->lowercase();

		$current  = $textObject->get();
		$expected = 'zażółć gęślą jaźń';
		$this->assertEquals($expected, $current);
	}

	public function testUppercase() : void
	{
		$textObject = new __\Text('Zażółć gęślą jaźń');
		$textObject->uppercase();

		$current  = $textObject->get();
		$expected = 'ZAŻÓŁĆ GĘŚLĄ JAŹŃ';
		$this->assertEquals($expected, $current);
	}

	public function testCut() : void
	{
		$textObject1 = new __\Text('Zażółć gęślą jaźń');
		$textObject1->cut(1, 5);

		$current  = $textObject1->get();
		$expected = 'ażółć';
		$this->assertEquals($expected, $current);

		$textObject2 = new __\Text('Zażółć gęślą jaźń');
		$textObject2->cut(-10, -4);

		$current  = $textObject2->get();
		$expected = 'gęślą ';
		$this->assertEquals($expected, $current);
	}

	public function testReplace() : void
	{
		// https://pl.wikipedia.org/wiki/Pangram#j%C4%99zyk_polski
		$textObject = new __\Text('Myślę: Fruń z płacht gąsko, jedź wbić nóż');

		$textObject->replace([
			'Myślę' => 'PrzeMYŚLĘ',
			'gąsko' => 'PTAKU',
			'nóż'   => 'igłę…',
		]);

		$current  = $textObject->get();
		$expected = 'PrzeMYŚLĘ: Fruń z płacht PTAKU, jedź wbić igłę…';
		$this->assertEquals($expected, $current);

		$textObject->replace([
			'przemyślę' => 'Myślę',
			'ptaku,'    => "gąsko,\n",
			'igłę…'     => 'nóż',
		], true);

		$current  = $textObject->get();
		$expected = "Myślę: Fruń z płacht gąsko,\n jedź wbić nóż";
		$this->assertEquals($expected, $current);
	}

	public function testCorrectTypography() : void
	{
		// Examples sources: https://pl.wikipedia.org, https://sjp.pwn.pl, https://encyklopedia.pwn.pl.
		$exampleCodeBefore = <<< TEXT
<h2 class="title">"A oto przykładowy tekst"</h2>
<p>Nie ma się nad czym zastanawiać, &quot;koń jaki jest, każdy widzi&quot;.</p>
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

		$textObject = new __\Text($exampleCodeBefore);
		$textObject->correctTypography(
			__\Text::TYPOGRAPHY_DASHES | __\Text::TYPOGRAPHY_ORPHANS |
			__\Text::TYPOGRAPHY_QUOTES | __\Text::TYPOGRAPHY_OTHER
		);

		$current  = $textObject->get();
		$expected = $exampleCodeAfter;
		$this->assertEquals($expected, $current);
	}

	public function testMakeFragment() : void
	{
		$textObject1 = new __\Text('Zażółć gęślą jaźń');
		$textObject1->makeFragment(12, '…');

		$current  = $textObject1->get();
		$expected = 'Zażółć gęślą…';
		$this->assertEquals($expected, $current);

		$textObject2 = new __\Text('Zażółć gęślą jaźń');
		$textObject2->makeFragment(15, '…');

		$current  = $textObject2->get();
		$expected = 'Zażółć gęślą…';  // Broken word should be removed.
		$this->assertEquals($expected, $current);
	}

	public function testMakeMiddleFragment() : void
	{
		$textObject = new __\Text('Zażółć gęślą jaźń');
		$textObject->makeMiddleFragment(12, ' (...) ');

		$current  = $textObject->get();
		$expected = 'Zażółć (...) jaźń';
		$this->assertEquals($expected, $current);
	}

	public function testMakeSlug() : void
	{
		$textObject = new __\Text('Zażółć  gęślą _ jaźń');
		$textObject->makeSlug();

		$current  = $textObject->get();
		$expected = 'zazolc-gesla-_-jazn';
		$this->assertEquals($expected, $current);
	}

	public function testFormatAsDateTime() : void
	{
		$textObject1 = new __\Text('1997-06-03 16:30');
		$textObject1->formatAsDateTime('%R %d.%m.%Y');

		$current  = $textObject1->get();
		$expected = '16:30 03.06.1997';
		$this->assertEquals($expected, $current);

		$unixTimestamp = time();
		$textObject2 = new __\Text($unixTimestamp);
		$textObject2->formatAsDateTime('%Y-%m-%d');

		$current  = $textObject2->get();
		$expected = strftime('%Y-%m-%d', $unixTimestamp);
		$this->assertEquals($expected, $current);
	}

	public function testFormatAsFileSize() : void
	{
		$textObject1 = new __\Text('914695416');
		$textObject1->formatAsFileSize();

		$current  = $textObject1->get();
		$expected = "872.3\u{00A0}MiB";
		$this->assertEquals($expected, $current);

		$textObject2 = new __\Text('914695416');
		$textObject2->formatAsFileSize(false);

		$current  = $textObject2->get();
		$expected = "914.7\u{00A0}MB";
		$this->assertEquals($expected, $current);

		$textObject3 = new __\Text('1022');
		$textObject3->formatAsFileSize();

		$current  = $textObject3->get();
		$expected = "1022\u{00A0}B";
		$this->assertEquals($expected, $current);

		$textObject4 = new __\Text('497338');
		$textObject4->formatAsFileSize();

		$current  = $textObject4->get();
		$expected = "485.7\u{00A0}KiB";
		$this->assertEquals($expected, $current);
	}

	public function testArrayAccess() : void
	{
		$textObject = new __\Text('Zazółć gęślą jaźń');

		$textObject[0]  = 'A';
		$textObject[4]  = 'B';
		$textObject[16] = 'C';

		$current  = $textObject->get();
		$expected = 'AazóBć gęślą jaźC';
		$this->assertEquals($expected, $current);

		$textObject[-17] = 'D';
		$textObject[-4]  = 'E';
		$textObject[-1]  = 'F';

		$current  = $textObject->get();
		$expected = 'DazóBć gęślą EaźF';
		$this->assertEquals($expected, $current);
	}

	public function testIterator() : void
	{
		$textObject = new __\Text('Zazółć gęślą jaźń');

		$current = '';
		foreach ($textObject as $char) {
			$current .= "\n$char";
		}
		$expected = "\nZ\na\nz\nó\nł\nć\n \ng\nę\nś\nl\ną\n \nj\na\nź\nń";
		$this->assertEquals($expected, $current);
	}
}