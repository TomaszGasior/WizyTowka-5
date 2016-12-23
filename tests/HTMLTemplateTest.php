<?php

/**
* WizyTówka 5 — unit test
*/
class HTMLTemplateTest extends PHPUnit\Framework\TestCase
{
	static private $_exampleTemplateName = 'exampleTemplate';
	static private $_exampleTemplateFile = 'exampleTemplate.php';
	static private $_exampleTemplatePath = '.';
	static private $_exampleTemplateCode = <<< 'EOL'
<!doctype html>
<meta charset="utf-8">
<title><?= $title ?></title>
<h1><?= $header ?></h1>
<p><?= $content ?></p>
EOL;
	static private $_expectedOutput = <<< 'EOL'
<!doctype html>
<meta charset="utf-8">
<title>Example title</title>
<h1>Header of page</h1>
<p>Content of page</p>
EOL;
	static private $_exampleVariables = [
		'title' => 'Example title',
		'header' => 'Header of page',
		'content' => 'Content of page',
	];

	static public function setUpBeforeClass()
	{
		file_put_contents(self::$_exampleTemplateFile, self::$_exampleTemplateCode);
	}

	static public function tearDownAfterClass()
	{
		unlink(self::$_exampleTemplateFile);
	}

	public function testRenderWithLocalName()
	{
		$this->expectOutputString(self::$_expectedOutput);

		$object = new WizyTowka\HTMLTemplate;
		$object->setTemplatePath('.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::$_exampleTemplateName);
	}

	public function testRenderWithGlobalName()
	{
		$this->expectOutputString(self::$_expectedOutput);

		$object = new WizyTowka\HTMLTemplate;
		$object->setTemplate(self::$_exampleTemplateName);
		$object->setTemplatePath('.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();
	}

	public function testRenderWithGlobalNameInConstructor()
	{
		$this->expectOutputString(self::$_expectedOutput);

		$object = new WizyTowka\HTMLTemplate(self::$_exampleTemplateName, '.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render();
	}

	public function testRenderWithOverwrittenName()
	{
		$this->expectOutputString(self::$_expectedOutput);

		$object = new WizyTowka\HTMLTemplate('nonexistentTemplate', '.');

		foreach (self::$_exampleVariables as $variable => $value) {
			$object->$variable = $value;
		}

		$object->render(self::$_exampleTemplateName);
	}

	/**
	 * @expectedException     WizyTowka\Exception
	 * @expectedExceptionCode 22
	 */
	public function testRenderWithoutName()
	{
		$object = new WizyTowka\HTMLTemplate;

		$object->render();
	}
}
