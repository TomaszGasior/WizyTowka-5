<?php

/**
* WizyTówka 5 — unit test
*/
class DatabasePDOTest extends TestCase
{
	static private $_exampleDatabaseFile = 'exampleSQLiteDatabase.db';

	static public function tearDownAfterClass()
	{
		@unlink(self::$_exampleDatabaseFile);
	}

	public function testWrapper()
	{
		$databasePDO = new WizyTowka\_Private\DatabasePDO('sqlite', self::$_exampleDatabaseFile);

		$this->assertFileExists(self::$_exampleDatabaseFile);

		$databasePDO->exec('CREATE TABLE exampleTable(column1 INTEGER); INSERT INTO exampleTable(column1) VALUES (45);');

		$current  = $databasePDO->query('SELECT column1 FROM exampleTable')->fetchColumn();
		$expected = '45';
		$this->assertEquals($expected, $current);
	}
}