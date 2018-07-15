<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HooksManagerTest extends TestCase
{
	public function testRunAction() : void
	{
		$hooksManager = new __\_Private\HooksManager;

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

		$hooksManager->addAction('exampleAction', $action2);
		$hooksManager->addAction('exampleAction', $action3);
		$hooksManager->addAction('exampleAction', $action1);

		$hooksManager->runAction('exampleAction', $randomText);

		$expected = "Function2:$randomText\nFunction1:$randomText";
		$this->expectOutputString($expected);
	}

	public function testApplyFilter() : void
	{
		$hooksManager = new __\_Private\HooksManager;

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

		$hooksManager->addFilter('exampleFilter', $filter3);
		$hooksManager->addFilter('exampleFilter', $filter2);
		$hooksManager->addFilter('exampleFilter', $filter1);

		$current  = $hooksManager->applyFilter('exampleFilter', $randomText);
		$expected = $filter1($filter2($filter3($randomText)));
		$this->assertEquals($expected, $current);
	}

	public function testRemoveAction() : void
	{
		$hooksManager = new __\_Private\HooksManager;

		$function = function()
		{
			echo 'I should not be called!';
		};

		$hooksManager->addAction('secondExampleAction', $function);
		$hooksManager->removeAction('secondExampleAction', $function);

		$hooksManager->runAction('secondExampleAction');

		$this->expectOutputString('');
	}

	/**
	* @expectedException     WizyTowka\_Private\HooksManagerException
	* @expectedExceptionCode 1
	*/
	public function testInvalidCallbackArguments() : void
	{
		$hooksManager = new __\_Private\HooksManager;

		$function = function($requiredArgument1, $requiredArgument2, $requiredArgument3)
		{
			echo 'This function has 3 required arguments!';
		};

		$hooksManager->addAction('anotherExampleAction', $function);
		$hooksManager->runAction('anotherExampleAction', 'Only one argument given…');
	}
}