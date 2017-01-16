<?php

/**
* WizyTówka 5 — unit test
*/
class DatabaseTest extends PHPUnit\Framework\TestCase
{
	static private $_databaseFile = 'exampleSQLiteDatabase.db';

	static public function tearDownAfterClass()
	{
		if (file_exists(self::$_databaseFile)) {
			unlink(self::$_databaseFile);
		}

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
		WizyTowka\Database::connect('sqlite', self::$_databaseFile);

		$this->assertTrue(file_exists(self::$_databaseFile));
	}

	public function testPDOAfterConnect()
	{
		$check = WizyTowka\Database::pdo() instanceof \PDO;

		$this->assertTrue($check);
	}

	public function testExecuteSQL()
	{
		WizyTowka\Database::executeSQL('CREATE TABLE exampleTable(column1 INTEGER); INSERT INTO exampleTable(column1) VALUES (45);');

		$current  = WizyTowka\Database::pdo()->query('SELECT column1 FROM exampleTable')->fetchColumn();
		$expected = '45';

		$this->assertEquals($current, $expected);
	}
}