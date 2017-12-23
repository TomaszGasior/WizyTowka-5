<?php

/**
* WizyTówka 5 — unit test
*/
class DatabaseTest extends TestCase
{
	static private $_exampleDatabaseFile = 'exampleSQLiteDatabase.db';

	static public function tearDownAfterClass()
	{
		@unlink(self::$_exampleDatabaseFile);

		WizyTowka\Database::disconnect();
	}

	/**
	* @expectedException     WizyTowka\DatabaseException
	* @expectedExceptionCode 1
	*/
	public function testPDOBeforeConnect()
	{
		WizyTowka\Database::pdo();
	}

	public function testConnect()
	{
		WizyTowka\Database::connect('sqlite', self::$_exampleDatabaseFile);

		$this->assertFileExists(self::$_exampleDatabaseFile);
	}

	public function testPDOAfterConnect()
	{
		$this->assertInstanceOf(\PDO::class, WizyTowka\Database::pdo());
	}

	public function testExecuteSQL()
	{
		WizyTowka\Database::executeSQL('CREATE TABLE exampleTable(column1 INTEGER); INSERT INTO exampleTable(column1) VALUES (45);');

		$current  = WizyTowka\Database::pdo()->query('SELECT column1 FROM exampleTable')->fetchColumn();
		$expected = '45';
		$this->assertEquals($expected, $current);
	}
}