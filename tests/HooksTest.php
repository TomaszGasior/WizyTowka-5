<?php

/**
* WizyTówka 5 — unit test
*/
class HooksTest extends PHPUnit\Framework\TestCase
{
	public function testRunAction()
	{
		$action1 = function($text)
		{
			echo 'Function1:', $text;
		};
		$action2 = function($text)
		{
			echo 'Function2:', $text;
		};
		$action3 = function()
		{
			echo "\n";
		};
		$randomText = uniqid();

		WizyTowka\Hooks::addAction('exampleAction', $action2);
		WizyTowka\Hooks::addAction('exampleAction', $action3);
		WizyTowka\Hooks::addAction('exampleAction', $action1);

		WizyTowka\Hooks::runAction('exampleAction', $randomText);

		$expected = "Function2:$randomText\nFunction1:$randomText";
		$this->expectOutputString($expected);
	}

	public function testApplyFilter()
	{
		$filter1 = function($text)
		{
			return strrev(strtoupper($text));
		};
		$filter2 = function($text)
		{
			$chars = str_split($text);
			$text = '';
			foreach ($chars as $char) {
				$text .= ' ' . $char;
			}
			return trim($text);
		};
		$filter3 = function($text)
		{
			return str_replace(' ', '_', $text);
		};
		$randomText = uniqid();

		WizyTowka\Hooks::addFilter('exampleFilter', $filter3);
		WizyTowka\Hooks::addFilter('exampleFilter', $filter2);
		WizyTowka\Hooks::addFilter('exampleFilter', $filter1);

		$current  = WizyTowka\Hooks::applyFilter('exampleFilter', $randomText);
		$expected = $filter1($filter2($filter3($randomText)));
		$this->assertEquals($expected, $current);
	}

	public function testRemoveAction()
	{
		$function = function()
		{
			echo 'I should not be called!';
		};

		WizyTowka\Hooks::addAction('secondExampleAction', $function);
		WizyTowka\Hooks::removeAction('secondExampleAction', $function);

		WizyTowka\Hooks::runAction('secondExampleAction');

		$this->expectOutputString('');
	}

	/**
	* @expectedException     WizyTowka\HooksException
	* @expectedExceptionCode 1
	*/
	public function testInvalidCallbackArguments()
	{
		$function = function($requiredArgument1, $requiredArgument2, $requiredArgument3)
		{
			echo 'This function has 3 required arguments!';
		};

		WizyTowka\Hooks::addAction('anotherExampleAction', $function);
		WizyTowka\Hooks::runAction('anotherExampleAction', 'Only one argument given…');
	}
}