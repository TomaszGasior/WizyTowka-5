<?php

/**
* WizyTówka 5 — unit test
*/
class DatabaseObjectTest extends PHPUnit\Framework\TestCase
{
	static private $_exampleClass;
	static private $_exampleClassJSON;
	static private $_exampleClassTime;

	static public function setUpBeforeClass()
	{
		// Connect to SQLite database in memory. Prepare database structure and content.
		WizyTowka\Database::connect('sqlite', ':memory:');
		WizyTowka\Database::executeSQL('
			CREATE TABLE exampleTable (primaryKey INTEGER PRIMARY KEY AUTOINCREMENT, column1 INTEGER, column2 TEXT);
			CREATE TABLE exampleTableJSON (primaryKey INTEGER PRIMARY KEY AUTOINCREMENT, dataJSON TEXT);
			CREATE TABLE exampleTableTime (primaryKey INTEGER PRIMARY KEY AUTOINCREMENT, updatedAt INTEGER, insertedAt INTEGER);
			INSERT INTO exampleTable(column1, column2) VALUES (100, "hundred"), (1000, "thousand");
		');

		// Example anonymous classes that extend abstract DatabaseObject class. PHP 7 syntax.
		self::$_exampleClass = new class() extends WizyTowka\DatabaseObject
		{
			static protected $_tableName = 'exampleTable';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'column1',
				'column2',
			];
		};
		self::$_exampleClassJSON = new class() extends WizyTowka\DatabaseObject
		{
			static protected $_tableName = 'exampleTableJSON';
			static protected $_tablePrimaryKey = 'primaryKey';
			static protected $_tableColumns = [
				'dataJSON',
			];
			static protected $_tableColumnsJSON = [
				'dataJSON',
			];
		};
		self::$_exampleClassTime = new class() extends WizyTowka\DatabaseObject
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
		};
	}

	static public function tearDownAfterClass()
	{
		WizyTowka\Database::disconnect();
	}

	// Helper function used to do convertion from object to array by foreach loop.
	private function convertObjectToArray($obj)
	{
		$arr = [];
		foreach ($obj as $key => $value) {
			$arr[$key] = $value;
		}
		return $arr;
	}

	public function testGetAll()
	{
		$objectsArray = self::$_exampleClass::getAll();

		$current  = array_map([$this,'convertObjectToArray'], $objectsArray);
		$expected = [
			['primaryKey' => '1', 'column1' => '100', 'column2' => 'hundred'],
			['primaryKey' => '2', 'column1' => '1000', 'column2' => 'thousand'],
		];
		$this->assertEquals($current, $expected);
	}

	public function testGetById()
	{
		$object = self::$_exampleClass::getById(2);

		$current  = $this->convertObjectToArray($object);
		$expected = ['primaryKey' => '2', 'column1' => '1000', 'column2' => 'thousand'];
		$this->assertEquals($current, $expected);
	}

	public function testSaveInsert()
	{
		$newObject = new self::$_exampleClass;
		$newObject->column1 = '10';
		$newObject->column2 = 'ten';
		$newObject->save();

		$object = self::$_exampleClass::getById($newObject->primaryKey);  // Primary key field is set after save() operation.

		$current  = $this->convertObjectToArray($object);
		$expected = ['primaryKey' => '3', 'column1' => '10', 'column2' => 'ten'];
		$this->assertEquals($current, $expected);
	}

	public function testSaveUpdate()
	{
		$editedObject = self::$_exampleClass::getById(1);
		$editedObject->column1 = '1024';
		$editedObject->column2 = 'one thousand twenty four';
		$editedObject->save();

		$object = self::$_exampleClass::getById(1);

		$current  = $this->convertObjectToArray($object);
		$expected = ['primaryKey' => '1', 'column1' => '1024', 'column2' => 'one thousand twenty four'];
		$this->assertEquals($current, $expected);
	}

	public function testDelete()
	{
		$removedObject = self::$_exampleClass::getById(1);
		$removedObject->delete();

		$object = self::$_exampleClass::getById(1);

		$this->assertFalse($object);
	}

	public function testJSONEncoding()
	{
		$exampleData = [
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
			'key4' => 'value4',
		];

		$newObject = new self::$_exampleClassJSON;
		foreach ($exampleData as $key => $value) {
			$newObject->dataJSON->$key = $value;
		}
		$newObject->save();

		$expected = (object)$exampleData;
		$current = $newObject->dataJSON;
		$this->assertEquals($current, $expected);

		$object = self::$_exampleClassJSON::getById($newObject->primaryKey);

		$expected = (object)$exampleData;
		$current = $object->dataJSON;
		$this->assertEquals($current, $expected);
	}

	public function testTimeAtInsert()
	{
		$newObject = new self::$_exampleClassTime;
		$newObject->save();

		$expected = time();
		$current = $newObject->insertedAt;
		$this->assertEquals($current, $expected);

		$object = self::$_exampleClassTime::getById(1);

		$current = $object->insertedAt;
		$this->assertEquals($current, $expected);
	}

	public function testTimeAtUpdate()
	{
		$editedObject = self::$_exampleClassTime::getById(1);
		$editedObject->save();

		$expected = time();
		$current = $editedObject->updatedAt;
		$this->assertEquals($current, $expected);

		$object1 = self::$_exampleClassTime::getById(1);

		$current = $object1->updatedAt;
		$this->assertEquals($current, $expected);
	}

	/**
	 * @expectedException     WizyTowka\WTException
	 * @expectedExceptionCode 10
	 */
	public function testDoNotEditPrimaryKey()
	{
		$object = new self::$_exampleClass;
		$object->primaryKey = 1;
	}
}