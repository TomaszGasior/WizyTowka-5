<?php

/**
* WizyTówka 5 — unit test
*/
class HooksTest extends PHPUnit\Framework\TestCase
{
	public function testRunAction()
	{
		$action1 = function($text) {
			echo 'Function1:', $text;
		};
		$action2 = function($text) {
			echo 'Function2:', $text;
		};
		$action3 = function() {
			echo "\n";
		};
		$randomText = uniqid();

		WizyTowka\Hooks::addAction('exampleAction', $action2);
		WizyTowka\Hooks::addAction('exampleAction', $action3);
		WizyTowka\Hooks::addAction('exampleAction', $action1);

		ob_start();
		WizyTowka\Hooks::runAction('exampleAction', $randomText);
		$output = ob_get_clean();

		$expected = "Function2:$randomText\nFunction1:$randomText";
		$this->assertEquals($output, $expected);
	}

	public function testApplyFilter()
	{
		$filter1 = function($text) {
			return strrev(strtoupper($text));
		};
		$filter2 = function($text) {
			$chars = str_split($text);
			$text = '';
			foreach ($chars as $char) {
				$text .= ' ' . $char;
			}
			return trim($text);
		};
		$filter3 = function($text) {
			return str_replace(' ', '_', $text);
		};
		$randomText = uniqid();

		WizyTowka\Hooks::addFilter('exampleFilter', $filter3);
		WizyTowka\Hooks::addFilter('exampleFilter', $filter2);
		WizyTowka\Hooks::addFilter('exampleFilter', $filter1);

		$returnedText = WizyTowka\Hooks::applyFilter('exampleFilter', $randomText);

		$expected = $filter1($filter2($filter3($randomText)));
		$this->assertEquals($returnedText, $expected);
	}

	public function testRemoveAction()
	{
		WizyTowka\Hooks::addAction('secondExampleAction', 'strrev');
		WizyTowka\Hooks::removeAction('secondExampleAction', 'strrev');

		$actionsArray = (new ReflectionClass('WizyTowka\\Hooks'))->getStaticProperties()['_actions'];

		$this->assertEmpty($actionsArray['secondExampleAction']);
	}

	/**
	 * @expectedException     WizyTowka\WTException
	 * @expectedExceptionCode 5
	 */
	public function testInvalidCallbackArguments()
	{
		$function = function($requiredArgument1, $requiredArgument2, $requiredArgument3) {
			echo 'This function has 3 required arguments!';
		};

		WizyTowka\Hooks::addAction('anotherExampleAction', $function);
		WizyTowka\Hooks::runAction('anotherExampleAction', 'Only one argument given…');
	}
}