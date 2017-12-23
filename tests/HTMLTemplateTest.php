<?php

/**
* WizyTówka 5 — unit test
*/
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
<h1>Header of page</h1>
<p>Content of page</p>
HTML;

	static private $_exampleVariables = [
		'title'   => 'Example title',
		'header'  => 'Header of page',
		'content' => 'Content of page',
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
		$object = new WizyTowka\HTMLTemplate;
		$object->setTemplatePath('.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::$_exampleTemplateName);

		$this->expectOutputString(self::$_expectedOutput);
	}

	public function testRenderWithGlobalName()
	{
		$object = new WizyTowka\HTMLTemplate;
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
		$object = new WizyTowka\HTMLTemplate(self::$_exampleTemplateName, '.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();

		$this->expectOutputString(self::$_expectedOutput);
	}

	public function testRenderWithOverwrittenName()
	{
		$object = new WizyTowka\HTMLTemplate('nonexistentTemplate', '.');

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
		$object = new WizyTowka\HTMLTemplate;

		$object->render();
	}
}
