<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLMenuTest extends TestCase
{
	public function testAdd()
	{
		$object = new __\HTMLMenu('exampleCSSClass');
		$object->append('Yahoo', 'http://yahoo.com', null, ['target' => '_blank']);
		$object->prepend('Bing', 'http://bing.com', null);
		$object->insert(-1, 'Google', 'http://google.com', 'google');
		$object->insert(4, 'Facebook', 'http://facebook.com');
		$object->append('PornHub', 'http://pornhub.com', null, [], false);  // Hidden.

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul class="exampleCSSClass">
	<li class="google"><a href="http://google.com">Google</a></li>
	<li><a href="http://bing.com">Bing</a></li>
	<li><a href="http://yahoo.com" target="_blank">Yahoo</a></li>
	<li><a href="http://facebook.com">Facebook</a></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testAddNested()
	{
		$objectNested = new __\HTMLMenu;
		$objectNested->prepend('File 3', 'file3.html');
		$objectNested->prepend('File 2', 'file2.html');
		$objectNested->prepend('File 1', 'file1.html');

		$object = new __\HTMLMenu;
		$object->append('Files', $objectNested);
		$object->append('Webpage 2', 'http://example.com');
		$object->prepend('Webpage 1', 'http://example.org');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><a href="http://example.org">Webpage 1</a></li>
	<li><span>Files</span><ul>
		<li><a href="file1.html">File 1</a></li>
		<li><a href="file2.html">File 2</a></li>
		<li><a href="file3.html">File 3</a></li>
	</ul></li>
	<li><a href="http://example.com">Webpage 2</a></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	/**
	* @expectedException     WizyTowka\HTMLMenuException
	* @expectedExceptionCode 2
	*/
	public function testAddWrongObject()
	{
		$object = new __\HTMLMenu;
		$object->prepend('Element', new \stdClass);
	}

	public function testReplace()
	{
		$object = new __\HTMLMenu;
		$object->insert(5, 'File 5', 'file5.html');
		$object->insert(7, 'File 7', 'file7.html');
		$object->insert(1, 'File 1', 'file1.html');
		$object->replace(5, 'File 5 replaced', 'replaced.html');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><a href="file1.html">File 1</a></li>
	<li><a href="replaced.html">File 5 replaced</a></li>
	<li><a href="file7.html">File 7</a></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRemoveByPosition()
	{
		$object = new __\HTMLMenu;
		$object->insert(7, 'File 7', 'file7.html');
		$object->insert(8, 'File 8', 'file8.html');
		$object->insert(1, 'File 1', 'file1.html');
		$object->insert(3, 'File 3', 'file3.html');
		$object->insert(5, 'File 5', 'file5.html');
		$object->removeByPosition(7);

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><a href="file1.html">File 1</a></li>
	<li><a href="file3.html">File 3</a></li>
	<li><a href="file5.html">File 5</a></li>
	<li><a href="file8.html">File 8</a></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRemoveByContent()
	{
		$object = new __\HTMLMenu;
		$object->append('File 1', 'file1.html');
		$object->append('File 2', 'file2.html');
		$object->append('File 2', 'file2.html');
		$object->append('File 3', 'file3.html');
		$object->removeByContent('file2.html');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><a href="file1.html">File 1</a></li>
	<li><a href="file3.html">File 3</a></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRemoveByLabel()
	{
		$object = new __\HTMLMenu;
		$object->append('File 1', 'file1.html');
		$object->append('File 2', 'file2.html');
		$object->append('File 2', 'file2.html');
		$object->append('File 3', 'file3.html');
		$object->removeByLabel('File 2');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><a href="file1.html">File 1</a></li>
	<li><a href="file3.html">File 3</a></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}
}