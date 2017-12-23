<?php

/**
* WizyTówka 5 — unit test
*/
class DatabaseObjectTest extends TestCase
{
	static private $_exampleDBObj;
	static private $_exampleDBObjJSON;
	static private $_exampleDBObjTime;

	static public function setUpBeforeClass()
	{
		// Connect to SQLite database in memory. Prepare database structure and content.
		WizyTowka\Database::connect('sqlite', ':memory:');
		WizyTowka\Database::executeSQL('
			CREATE TABLE exampleTable (
				primaryKey INTEGER PRIMARY KEY AUTOINCREMENT,
				column1 INTEGER,
				column2 TEXT
			);
			CREATE TABLE exampleTableJSON (
				primaryKey INTEGER PRIMARY KEY AUTOINCREMENT,
				dataJSON TEXT
			);
			CREATE TABLE exampleTableTime (
				primaryKey INTEGER PRIMARY KEY AUTOINCREMENT,
				updatedAt INTEGER,
				insertedAt INTEGER
			);
			INSERT INTO exampleTable(column1, column2) VALUES (100, "hundred"), (1000, "thousand");
		');

		// Example anonymous classes that extend abstract DatabaseObject class. PHP 7 syntax.
		self::$_exampleDBObj = get_class(new class() extends WizyTowka\DatabaseObject
		{
			static protected $_tableName = 'exampleTable';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'column1',
				'column2',
			];
		});
		self::$_exampleDBObjJSON = get_class(new class() extends WizyTowka\DatabaseObject
		{
			static protected $_tableName = 'exampleTableJSON';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'dataJSON',
			];
			static protected $_tableColumnsJSON = [
				'dataJSON',
			];
		});
		self::$_exampleDBObjTime = get_class(new class() extends WizyTowka\DatabaseObject
		{
			static protected $_tableName = 'exampleTableTime';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'updatedAt',
				'insertedAt',
			];
			static protected $_tableColumnsTimeAtInsert = [
				'insertedAt',
			];
			static protected $_tableColumnsTimeAtUpdate = [
				'updatedAt',
			];
		});
	}

	static public function tearDownAfterClass()
	{
		WizyTowka\Database::disconnect();
	}

	public function testGetAll()
	{
		$objectsArray = self::$_exampleDBObj::getAll();

		$current  = array_map('iterator_to_array', $objectsArray);
		$expected = [
			['primaryKey' => '1', 'column1' => '100', 'column2' => 'hundred'],
			['primaryKey' => '2', 'column1' => '1000', 'column2' => 'thousand'],
		];
		$this->assertEquals($expected, $current);
	}

	public function testGetById()
	{
		$object = self::$_exampleDBObj::getById(2);

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '2', 'column1' => '1000', 'column2' => 'thousand'];
		$this->assertEquals($expected, $current);
	}

	public function testSaveInsert()
	{
		$newObject = new self::$_exampleDBObj;
		$newObject->column1 = '10';
		$newObject->column2 = 'ten';
		$newObject->save();

		$object = self::$_exampleDBObj::getById($newObject->primaryKey);  // Primary key field is set after save() operation.

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '3', 'column1' => '10', 'column2' => 'ten'];
		$this->assertEquals($expected, $current);
	}

	public function testSaveUpdate()
	{
		$editedObject = self::$_exampleDBObj::getById(1);
		$editedObject->column1 = '1024';
		$editedObject->column2 = 'one thousand twenty four';
		$editedObject->save();

		$object = self::$_exampleDBObj::getById(1);

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '1', 'column1' => '1024', 'column2' => 'one thousand twenty four'];
		$this->assertEquals($expected, $current);
	}

	public function testDelete()
	{
		$removedObject = self::$_exampleDBObj::getById(1);
		$removedObject->delete();

		$object = self::$_exampleDBObj::getById(1);

		$this->assertFalse($object);
	}

	public function testClone()
	{
		$originalObject = self::$_exampleDBObj::getById(2);
		$clonedObject   = clone $originalObject;

		// Cloned object should be treated as newly created, primary key field should be empty.
		$this->assertNull($clonedObject->primaryKey);

		$clonedObject->save();

		$object = self::$_exampleDBObj::getById($clonedObject->primaryKey);  // Primary key field is set after save() operation.

		$current  = iterator_to_array($object);
		$expected = ['primaryKey' => '4', 'column1' => '1000', 'column2' => 'thousand'];
		$this->assertEquals($expected, $current);
	}

	public function testJSONEncoding()
	{
		$exampleData = [
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
			'key4' => 'value4',
		];

		$newObject = new self::$_exampleDBObjJSON;
		foreach ($exampleData as $key => $value) {
			$newObject->dataJSON->$key = $value;
		}
		$newObject->save();

		$current  = $newObject->dataJSON;
		$expected = (object)$exampleData;
		$this->assertEquals($expected, $current);

		$object = self::$_exampleDBObjJSON::getById($newObject->primaryKey);

		$current  = $object->dataJSON;
		$expected = (object)$exampleData;
		$this->assertEquals($expected, $current);
	}

	public function testTimeAtInsert()
	{
		$newObject = new self::$_exampleDBObjTime;
		$newObject->save();

		$current  = $newObject->insertedAt;
		$expected = time();
		$this->assertEquals($expected, $current);

		$object = self::$_exampleDBObjTime::getById(1);

		$current = $object->insertedAt;
		$this->assertEquals($expected, $current);
	}

	public function testTimeAtUpdate()
	{
		$editedObject = self::$_exampleDBObjTime::getById(1);
		$editedObject->save();

		$current  = $editedObject->updatedAt;
		$expected = time();
		$this->assertEquals($expected, $current);

		$object = self::$_exampleDBObjTime::getById(1);

		$current = $object->updatedAt;
		$this->assertEquals($expected, $current);
	}

	/**
	* @expectedException     WizyTowka\DatabaseObjectException
	* @expectedExceptionCode 1
	*/
	public function testDoNotEditPrimaryKey()
	{
		$object = new self::$_exampleDBObj;

		$object->primaryKey = 1;
	}
}