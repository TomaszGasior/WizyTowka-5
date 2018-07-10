<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLTemplateTest extends TestCase
{
	private const EXAMPLE_TEMPLATE_NAME = 'HTMLTemplate_exampleTemplate';
	private const EXAMPLE_TEMPLATE_FILE = self::EXAMPLE_TEMPLATE_NAME . '.php';
	private const EXAMPLE_TEMPLATE_PATH = TEMP_FILES_DIR;

	private const EXAMPLE_TEMPLATE_CODE = <<< 'HTML'
<!doctype html>
<meta charset="utf-8">
<title><?= $title ?></title>
<h1><?= $header ?></h1>
<p><?= $content ?></p>
HTML;

	private const EXAMPLE_VARIABLES = [
		'title'   => 'Example title',
		'header'  => '"Header of page"',
		'content' => 'Content <br> of page',
	];

	private const EXPECTED_OUTPUT = <<< 'HTML'
<!doctype html>
<meta charset="utf-8">
<title>Example title</title>
<h1>&quot;Header of page&quot;</h1>
<p>Content &lt;br&gt; of page</p>
HTML;
	private const EXPECTED_OUTPUT_RAW = <<< 'HTML'
<!doctype html>
<meta charset="utf-8">
<title>Example title</title>
<h1>"Header of page"</h1>
<p>Content <br> of page</p>
HTML;

	static public function setUpBeforeClass() : void
	{
		file_put_contents(
			self::EXAMPLE_TEMPLATE_PATH . '/' . self::EXAMPLE_TEMPLATE_FILE,
			self::EXAMPLE_TEMPLATE_CODE
		);
	}

	static public function tearDownAfterClass() : void
	{
		unlink(self::EXAMPLE_TEMPLATE_PATH . '/' . self::EXAMPLE_TEMPLATE_FILE);
	}

	public function testRenderWithLocalName() : void
	{
		$object = new __\HTMLTemplate;
		$object->setTemplatePath(self::EXAMPLE_TEMPLATE_PATH);

		foreach (self::EXAMPLE_VARIABLES as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::EXAMPLE_TEMPLATE_NAME);

		$this->expectOutputString(self::EXPECTED_OUTPUT);
	}

	public function testRenderWithGlobalName() : void
	{
		$object = new __\HTMLTemplate;
		$object->setTemplate(self::EXAMPLE_TEMPLATE_NAME);
		$object->setTemplatePath(self::EXAMPLE_TEMPLATE_PATH);

		foreach (self::EXAMPLE_VARIABLES as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();

		$this->expectOutputString(self::EXPECTED_OUTPUT);
	}

	public function testRenderWithGlobalNameInConstructor() : void
	{
		$object = new __\HTMLTemplate(self::EXAMPLE_TEMPLATE_NAME, self::EXAMPLE_TEMPLATE_PATH);

		foreach (self::EXAMPLE_VARIABLES as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();

		$this->expectOutputString(self::EXPECTED_OUTPUT);
	}

	public function testRenderWithOverwrittenName() : void
	{
		$object = new __\HTMLTemplate('nonexistentTemplate', self::EXAMPLE_TEMPLATE_PATH);

		foreach (self::EXAMPLE_VARIABLES as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::EXAMPLE_TEMPLATE_NAME);

		$this->expectOutputString(self::EXPECTED_OUTPUT);
	}

	/**
	* @expectedException     WizyTowka\HTMLTemplateException
	* @expectedExceptionCode 1
	*/
	public function testRenderWithoutName() : void
	{
		$object = new __\HTMLTemplate;

		$object->render();
	}

	public function testSetRaw() : void
	{
		$object = new __\HTMLTemplate;
		$object->setTemplatePath(self::EXAMPLE_TEMPLATE_PATH);

		foreach (self::EXAMPLE_VARIABLES as $variable => $value) {
			$object->setRaw($variable, $value);
		}

		$object->render(self::EXAMPLE_TEMPLATE_NAME);

		$this->expectOutputString(self::EXPECTED_OUTPUT_RAW);
	}

	/**
	* @expectedException              WizyTowka\HTMLTemplateException
	* @expectedExceptionCode          2
	* @expectedExceptionMessageRegExp /wrongVariablePhpTempStream/
	*/
	public function testSetEscapedWithWrongType() : void
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
		$object->iterator = new class implements \IteratorAggregate
		{
			public function getIterator() : iterable
			{
				yield true;
			}
		};

		$object->wrongVariablePhpTempStream = fopen('php://temp', 'r');
		// Variable name is kept in exception message.
	}
}