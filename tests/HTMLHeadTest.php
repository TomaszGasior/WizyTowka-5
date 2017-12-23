<?php

/**
* WizyTówka 5 — unit test
*/
class HTMLHeadTest extends TestCase
{
	public function testBaseAndTitle()
	{
		$object = new WizyTowka\HTMLHead;

		$object->base('http://example.org');
		$object->title('Title of page');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<base href="http://example.org">
<title>Title of page</title>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testScripts()
	{
		$object = new WizyTowka\HTMLHead;

		$object->script('script.js', ['defer' => true]);
		$object->script('script.js', ['async' => true]);

		$object->inlineScript('alert("hey!")');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<script src="script.js" defer></script>
<script src="script.js" async></script>
<script>alert("hey!")</script>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testStyles()
	{
		$object = new WizyTowka\HTMLHead;

		$object->stylesheet('stylesheet.css');
		$object->stylesheet('stylesheetMobile.css', ['media' => 'all and (max-width: 900px)']);

		$object->inlineStylesheet('body{color:red;}');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<link rel="stylesheet" href="stylesheet.css">
<link rel="stylesheet" href="stylesheetMobile.css" media="all and (max-width: 900px)">
<style>body{color:red;}</style>
HTML;
		$this->assertHTMLEquals($expected, $current);

		// Remove stylesheets with specified name.
		$object->removeStylesheet('stylesheetMobile.css');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<link rel="stylesheet" href="stylesheet.css">
<style>body{color:red;}</style>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testMeta()
	{
		$object = new WizyTowka\HTMLHead;

		$object->meta('description', 'HTML tutorial: "<HEAD>" tag examples');
		$object->meta('keywords', 'tutorial, coding, website');
		$object->httpEquiv('refresh', '0; url=http://example.org');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<meta name="description" content="HTML tutorial: &quot;&lt;HEAD&gt;&quot; tag examples">
<meta name="keywords" content="tutorial, coding, website">
<meta http-equiv="refresh" content="0; url=http://example.org">
HTML;
		$this->assertHTMLEquals($expected, $current);

		// Remove all "description" meta tags.
		$object->removeMeta('description');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<meta name="keywords" content="tutorial, coding, website">
<meta http-equiv="refresh" content="0; url=http://example.org">
HTML;
		$this->assertHTMLEquals($expected, $current);

		// Remove "keywords" meta tag with specified value.
		$object->removeMeta('keywords', 'tutorial, coding, website');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<meta http-equiv="refresh" content="0; url=http://example.org">
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testAssetsPath()
	{
		$object = new WizyTowka\HTMLHead;

		$object->setAssetsPath('assets');
		$object->setAssetsPathBase('http://localhost');
		$object->stylesheet('stylesheet.min.css');
		$object->stylesheet('http://example.org/stylesheet.min.css');
		$object->setAssetsPathBase('');

		$object->setAssetsPath('somewhere');
		$object->script('script.js');
		$object->script('https://example.org/script.js');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<link rel="stylesheet" href="http://localhost/assets/stylesheet.min.css">
<link rel="stylesheet" href="http://example.org/stylesheet.min.css">
<script src="somewhere/script.js"></script>
<script src="https://example.org/script.js"></script>
HTML;
		$this->assertHTMLEquals($expected, $current);

		$object->restoreAssetsPath();
		$object->link('icon', 'favicon.png');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<link rel="stylesheet" href="http://localhost/assets/stylesheet.min.css">
<link rel="stylesheet" href="http://example.org/stylesheet.min.css">
<script src="somewhere/script.js"></script>
<script src="https://example.org/script.js"></script>
<link rel="icon" href="assets/favicon.png">
HTML;
		$this->assertHTMLEquals($expected, $current);
	}
}