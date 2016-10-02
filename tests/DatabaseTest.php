<?php

class DatabaseTest extends PHPUnit\Framework\TestCase
{
	static private $databaseFile = 'exampleSQLiteDatabase.db';

	/**
	 * @expectedException        Exception
	 * @expectedExceptionMessage Database connection was not established properly.
	 */
	public function testPDOBeforeConnect()
	{
		WizyTowka\Database::pdo();
	}

	public function testConnect()
	{
		WizyTowka\Database::connect('sqlite', self::$databaseFile);

		$this->assertTrue(file_exists(self::$databaseFile));
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

	static public function tearDownAfterClass()
	{
		if (file_exists(self::$databaseFile)) {
			unlink(self::$databaseFile);
		}

		WizyTowka\Database::disconnect();
	}
}