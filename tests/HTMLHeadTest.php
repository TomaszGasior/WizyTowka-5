<?php

/**
* WizyTówka 5 — unit test
*/
class HTMLHeadTest extends PHPUnit\Framework\TestCase
{
	public function testBaseAndTitle()
	{
		$object = new WizyTowka\HTMLHead;
		$object->setBase('http://example.org');
		$object->setTitle('Title of page');

		$current = (string)$object;
		$expected = '<base href="http://example.org">' . '<title>Title of page</title>';
		$this->assertEquals($expected, $current);
	}

	public function testScripts()
	{
		$object = new WizyTowka\HTMLHead;
		$object->setTitle('Example');

		$object->setAssetsPath('example/assetsDir');
		$object->addScript('script.js');
		$object->setAssetsPath('');
		$object->addScript('script.js', true);

		$object->addInlineScript('alert("hey!")');

		$current = (string)$object;
		$expected = '<title>Example</title>';
		$expected .= '<script src="example/assetsDir/script.js" defer></script>';
		$expected .= '<script src="script.js" async></script>';
		$expected .= '<script>alert("hey!")</script>';
		$this->assertEquals($expected, $current);
	}

	public function testStyles()
	{
		$object = new WizyTowka\HTMLHead;
		$object->setTitle('Example');

		$object->setAssetsPath('example/assetsDir');
		$object->addStyle('stylesheet.css');
		$object->setAssetsPath('');
		$object->addStyle('stylesheet.css', 'all and (max-width: 900px)');

		$object->addInlineStyle('body{color:red;}');

		$current = (string)$object;
		$expected = '<title>Example</title>';
		$expected .= '<link rel="stylesheet" href="example/assetsDir/stylesheet.css">';
		$expected .= '<link rel="stylesheet" href="stylesheet.css" media="all and (max-width: 900px)">';
		$expected .= '<style>body{color:red;}</style>';
		$this->assertEquals($expected, $current);
	}

	public function testMetaTags()
	{
		$object = new WizyTowka\HTMLHead;
		$object->setTitle('Example');

		$object->setMeta('description', 'HTML tutorial: "<HEAD>" tag examples');
		$object->setMeta('keywords', 'html, lesson, tutorial, coding, website, programming');
		$object->setHttpEquiv('refresh', '0; url=http://example.org');

		$current = (string)$object;
		$expected = '<title>Example</title>';
		$expected .= '<meta http-equiv="refresh" content="0; url=http://example.org">';
		$expected .= '<meta name="description" content="HTML tutorial: &quot;&lt;HEAD&gt;&quot; tag examples">';
		$expected .= '<meta name="keywords" content="html, lesson, tutorial, coding, website, programming">';
		$this->assertEquals($expected, $current);
	}

	public function testAssetsRemoving()
	{
		$object = new WizyTowka\HTMLHead;
		$object->setTitle('Example');

		$object->setAssetsPath('');
		$object->addStyle('style1.css');
		$object->addStyle('style2.css');
		$object->setAssetsPath('example/example');
		$object->addStyle('style1.css');

		$object->setAssetsPath('');
		$object->addScript('script1.js');
		$object->addScript('script2.js');
		$object->setAssetsPath('example/something/else');
		$object->addScript('script1.js');

		$object->removeStyle('style1.css');
		$object->removeScript('script1.js');

		$current = (string)$object;
		$expected = '<title>Example</title>';
		$expected .= '<link rel="stylesheet" href="style2.css">';
		$expected .= '<script src="script2.js" defer></script>';
		$this->assertEquals($expected, $current);
	}
}
