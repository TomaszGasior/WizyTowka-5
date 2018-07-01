<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLTemplateTest extends TestCase
{
	static private $_exampleTemplateName = 'exampleTemplate';
	static private $_exampleTemplateFile = 'exampleTemplate.php';
	static private $_exampleTemplatePath = '.';

	static private $_exampleTemplateCode = <<< 'HTML'
<!doctype html>
<meta charset="utf-8">
<title><?= $title ?></title>
<h1><?= $header ?></h1>
<p><?= $content ?></p>
HTML;

	static private $_expectedOutput = <<< 'HTML'
<!doctype html>
<meta charset="utf-8">
<title>Example title</title>
<h1>&quot;Header of page&quot;</h1>
<p>Content &lt;br&gt; of page</p>
HTML;

	static private $_expectedOutputRaw = <<< 'HTML'
<!doctype html>
<meta charset="utf-8">
<title>Example title</title>
<h1>"Header of page"</h1>
<p>Content <br> of page</p>
HTML;

	static private $_exampleVariables = [
		'title'   => 'Example title',
		'header'  => '"Header of page"',
		'content' => 'Content <br> of page',
	];

	static public function setUpBeforeClass()
	{
		file_put_contents(self::$_exampleTemplateFile, self::$_exampleTemplateCode);
	}

	static public function tearDownAfterClass()
	{
		@unlink(self::$_exampleTemplateFile);
	}

	public function testRenderWithLocalName()
	{
		$object = new __\HTMLTemplate;
		$object->setTemplatePath('.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::$_exampleTemplateName);

		$this->expectOutputString(self::$_expectedOutput);
	}

	public function testRenderWithGlobalName()
	{
		$object = new __\HTMLTemplate;
		$object->setTemplate(self::$_exampleTemplateName);
		$object->setTemplatePath('.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();

		$this->expectOutputString(self::$_expectedOutput);
	}

	public function testRenderWithGlobalNameInConstructor()
	{
		$object = new __\HTMLTemplate(self::$_exampleTemplateName, '.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();

		$this->expectOutputString(self::$_expectedOutput);
	}

	public function testRenderWithOverwrittenName()
	{
		$object = new __\HTMLTemplate('nonexistentTemplate', '.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::$_exampleTemplateName);

		$this->expectOutputString(self::$_expectedOutput);
	}

	/**
	* @expectedException     WizyTowka\HTMLTemplateException
	* @expectedExceptionCode 1
	*/
	public function testRenderWithoutName()
	{
		$object = new __\HTMLTemplate;

		$object->render();
	}

	public function testSetRaw()
	{
		$object = new __\HTMLTemplate;
		$object->setTemplatePath('.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->setRaw($variable, $value);
		}

		$object->render(self::$_exampleTemplateName);

		$this->expectOutputString(self::$_expectedOutputRaw);
	}

	/**
	* @expectedException              WizyTowka\HTMLTemplateException
	* @expectedExceptionCode          2
	* @expectedExceptionMessageRegExp /wrongVariablePhpTempStream/
	*/
	public function testSetEscapedWithWrongType()
	{
		$object = new __\HTMLTemplate;

		// Types allowed for automatically escaped variables are:
		// integer, float, boolean, null, array, stdClass object, Traversable object, HTMLTemplate or HTMLTag object.
		// If value shouldn't be escaped use setRaw() instead.

		$object->integer = 10;
		$object->float   = 10.5;
		$object->boolean = true;
		$object->null    = null;

		$object->stdClass = new \stdClass;
		$object->iterator = new class() implements \IteratorAggregate
		{
			public function getIterator()
			{
				yield true;
			}
		};

		$object->wrongVariablePhpTempStream = fopen('php://temp', 'r');
		// Variable name is kept in exception message.
	}
}