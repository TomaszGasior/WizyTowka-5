<?php

/**
* WizyTówka 5 — unit test
*/
class HTMLMenuTest extends PHPUnit\Framework\TestCase
{
	public function testAdd()
	{
		$object = new WizyTowka\HTMLMenu('exampleCSSClass');
		$object->add('Google', 'http://google.com', 'google');
		$object->add('Yahoo', 'http://yahoo.com', null, null, true);
		$object->add('Facebook', 'http://facebook.com');
		$object->add('Bing', 'http://bing.com', null, 2);

		$current = (string)$object;
		$expected = '<ul class="exampleCSSClass">';
		$expected .= '<li class="google"><a href="http://google.com">Google</a></li>';
		$expected .= '<li><a href="http://bing.com">Bing</a></li>';
		$expected .= '<li><a href="http://yahoo.com" target="_blank">Yahoo</a></li>';
		$expected .= '<li><a href="http://facebook.com">Facebook</a></li>';
		$expected .= '</ul>';
		$this->assertEquals($expected, $current);
	}

	public function testAddNested()
	{
		$objectNested = new WizyTowka\HTMLMenu;
		$objectNested->add('File 1', 'file1.html');
		$objectNested->add('File 2', 'file2.html');
		$objectNested->add('File 3', 'file3.html');

		$object = new WizyTowka\HTMLMenu;
		$object->add('Webpage 1', 'http://example.org');
		$object->add('Files', $objectNested);
		$object->add('Webpage 2', 'http://example.com');

		$current = (string)$object;
		$expected = '<ul>';
		$expected .= '<li><a href="http://example.org">Webpage 1</a></li>';
		$expected .= '<li>Files';
		$expected .= '<ul>';
		$expected .= '<li><a href="file1.html">File 1</a></li>';
		$expected .= '<li><a href="file2.html">File 2</a></li>';
		$expected .= '<li><a href="file3.html">File 3</a></li>';
		$expected .= '</ul>';
		$expected .= '</li>';
		$expected .= '<li><a href="http://example.com">Webpage 2</a></li>';
		$expected .= '</ul>';
	}

	/**
	 * @expectedException     WizyTowka\Exception
	 * @expectedExceptionCode 26
	 */
	public function testAddWrongObject()
	{
		$object = new WizyTowka\HTMLMenu;
		$object->add('Element', new stdClass);
	}

	public function testRemove()
	{
		$object = new WizyTowka\HTMLMenu;
		$object->add('File 1', 'file1.html');
		$object->add('File 2', 'file2.html');
		$object->add('File 2', 'file2.html');
		$object->add('File 3', 'file3.html');
		$object->remove('File 2');

		$current = (string)$object;
		$expected = '<ul>';
		$expected .= '<li><a href="file1.html">File 1</a></li>';
		$expected .= '<li><a href="file3.html">File 3</a></li>';
		$expected .= '</ul>';
		$this->assertEquals($expected, $current);
	}
}