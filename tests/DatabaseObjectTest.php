<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class DatabaseObjectTest extends TestCase
{
	static private $_exampleClass;
	static private $_exampleClassExtra;

	static public function setUpBeforeClass() : void
	{
		// Connect to SQLite database in memory. Prepare database structure and content.
		$databasePDO = new __\_Private\DatabasePDO('sqlite', ':memory:');
		$databasePDO->exec('
			CREATE TABLE exampleTable (
				primaryKey INTEGER PRIMARY KEY AUTOINCREMENT,
				column1 INTEGER,
				column2 TEXT
			);
			CREATE TABLE exampleTableExtra (
				primaryKey INTEGER PRIMARY KEY AUTOINCREMENT,
				dataJSON TEXT,
				updatedAt INTEGER,
				insertedAt INTEGER
			);
			INSERT INTO exampleTable(column1, column2) VALUES (100, "hundred"), (1000, "thousand");
		');
		__\WT()->overwrite('database', $databasePDO);   // DatabaseObject needs WT()->database.

		// Example class extending abstract DatabaseObject class.
		self::$_exampleClass = get_class(new class extends __\DatabaseObject
		{
			static protected $_tableName = 'exampleTable';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'column1',
				'column2',
			];
		});

		// Second example class with extra features like JSON data encoding.
		self::$_exampleClassExtra = get_class(new class extends __\DatabaseObject
		{
			static protected $_tableName = 'exampleTableExtra';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'dataJSON',
				'updatedAt',
				'insertedAt',
			];
			static protected $_tableColumnsJSON = [
				'dataJSON',
			];
			static protected $_tableColumnsTimeAtInsert = [
				'insertedAt',
			];
			static protected $_tableColumnsTimeAtUpdate = [
				'updatedAt',
			];
		});
	}

	static public function tearDownAfterClass() : void
	{
		__\WT()->overwrite('database', null);
	}

	public function testGetAll() : void
	{
		$objectsArray = self::$_exampleClass::getAll();

		$current  = array_map('iterator_to_array', $objectsArray);
		$expected = [
			['primaryKey' => '1', 'column1' => '100', 'column2' => 'hundred'],
			['primaryKey' => '2', 'column1' => '1000', 'column2' => 'thousand'],
		];
		$this->assertEquals($expected, $current);
	}

	public function testGetById() : void
	{
		$object = self::$_exampleClass::getById(2);

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '2', 'column1' => '1000', 'column2' => 'thousand'];
		$this->assertEquals($expected, $current);
	}

	public function testSaveInsert() : void
	{
		$newObject = new self::$_exampleClass;
		$newObject->column1 = '10';
		$newObject->column2 = 'ten';
		$newObject->save();

		$object = self::$_exampleClass::getById($newObject->primaryKey);  // Primary key field is set after save() operation.

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '3', 'column1' => '10', 'column2' => 'ten'];
		$this->assertEquals($expected, $current);
	}

	public function testSaveUpdate() : void
	{
		$editedObject = self::$_exampleClass::getById(1);
		$editedObject->column1 = '1024';
		$editedObject->column2 = 'one thousand twenty four';
		$editedObject->save();

		$object = self::$_exampleClass::getById(1);

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '1', 'column1' => '1024', 'column2' => 'one thousand twenty four'];
		$this->assertEquals($expected, $current);
	}

	public function testDelete() : void
	{
		$removedObject = self::$_exampleClass::getById(1);
		$removedObject->delete();

		$object = self::$_exampleClass::getById(1);

		$this->assertNull($object);
	}

	public function testClone() : void
	{
		$originalObject = self::$_exampleClass::getById(2);
		$clonedObject   = clone $originalObject;

		// Cloned object should be treated as newly created, primary key field should be empty.
		$this->assertNull($clonedObject->primaryKey);

		$clonedObject->save();

		$object = self::$_exampleClass::getById($clonedObject->primaryKey);  // Primary key field is set after save() operation.

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '4', 'column1' => '1000', 'column2' => 'thousand'];
		$this->assertEquals($expected, $current);
	}

	/**
	* @expectedException     WizyTowka\DatabaseObjectException
	* @expectedExceptionCode 1
	*/
	public function testDoNotEditPrimaryKey() : void
	{
		$object = new self::$_exampleClass;

		$object->primaryKey = 1;
	}

	public function testTimeAtInsert() : void
	{
		$newObject = new self::$_exampleClassExtra;
		$newObject->save();

		$current  = $newObject->insertedAt;
		$expected = time();
		$this->assertEquals($expected, $current);

		$object = self::$_exampleClassExtra::getById(1);

		$current = $object->insertedAt;
		$this->assertEquals($expected, $current);
	}

	public function testTimeAtUpdate() : void
	{
		$editedObject = self::$_exampleClassExtra::getById(1);
		$editedObject->save();

		$current  = $editedObject->updatedAt;
		$expected = time();
		$this->assertEquals($expected, $current);

		$object = self::$_exampleClassExtra::getById(1);

		$current = $object->updatedAt;
		$this->assertEquals($expected, $current);
	}

	public function testJSONEncoding() : void
	{
		$exampleData = (object)[
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
			'key4' => 'value4',
		];

		$newObject = new self::$_exampleClassExtra;
		foreach ($exampleData as $key => $value) {
			$newObject->dataJSON->$key = $value;
		}
		$newObject->save();

		$current  = $newObject->dataJSON;
		$expected = $exampleData;
		$this->assertEquals($expected, $current);

		$object = self::$_exampleClassExtra::getById($newObject->primaryKey);

		$current  = $object->dataJSON;
		$expected = (object)$exampleData;
		$this->assertEquals($expected, $current);
	}
}