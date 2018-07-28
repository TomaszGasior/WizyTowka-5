<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class ReadOnlyArrayTest extends TestCase
{
	private const EXAMPLE_DATA = [
		'data_1' => 'f0eb5aad5cdcb936ccf4cd6f95bb09f5aa660d9c',
		'data_2' => 'c4190852ff3a38b8e5352d0f595960d94f72fdd8',
		'data_3' => ['3f5033867', 'b963901faebe10', '6b0cfdde4f7adac47'],
		'data_4' => 766369241,
		'data_5' => false,
	];

	public function testRead() : void
	{
		$exampleArray = new __\_Private\ReadOnlyArray(self::EXAMPLE_DATA);

		foreach ($exampleArray as $key => $value) {
			$current  = $value;
			$expected = self::EXAMPLE_DATA[$key];
			$this->assertEquals($expected, $current);

			$current  = $exampleArray[$key];
			$expected = self::EXAMPLE_DATA[$key];
			$this->assertEquals($expected, $current);
		}
	}

	public function testCountable() : void
	{
		$exampleArray = new __\_Private\ReadOnlyArray(self::EXAMPLE_DATA);

		$current  = count($exampleArray);
		$expected = count(self::EXAMPLE_DATA);
		$this->assertEquals($expected, $current);
	}

	/**
	* @expectedException              WizyTowka\_Private\ReadOnlyArrayException
	* @expectedExceptionCode          1
	* @expectedExceptionMessageRegExp /\$exampleArray\['data_1'\]/
	*/
	public function testModifyWrite() : void
	{
		$exampleArray = new __\_Private\ReadOnlyArray(self::EXAMPLE_DATA, 'exampleArray');

		$exampleArray['data_1'] = 'changed';
	}

	/**
	* @expectedException              WizyTowka\_Private\ReadOnlyArrayException
	* @expectedExceptionCode          1
	* @expectedExceptionMessageRegExp /\$exampleArray\['data_3'\]/
	*/
	public function testModifyUnset() : void
	{
		$exampleArray = new __\_Private\ReadOnlyArray(self::EXAMPLE_DATA, 'exampleArray');

		unset($exampleArray['data_3']);
	}
}