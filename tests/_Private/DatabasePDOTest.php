<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class DatabasePDOTest extends TestCase
{
	private const DATABASE_FILE = TEMP_FILES_DIR . '/DatabasePDO_database.db';

	static public function tearDownAfterClass() : void
	{
		unlink(self::DATABASE_FILE);
	}

	public function testWrapper() : void
	{
		$databasePDO = new __\_Private\DatabasePDO('sqlite', self::DATABASE_FILE);

		$this->assertFileExists(self::DATABASE_FILE);

		$databasePDO->exec('CREATE TABLE exampleTable(column1 INTEGER); INSERT INTO exampleTable(column1) VALUES (45);');

		$current  = $databasePDO->query('SELECT column1 FROM exampleTable')->fetchColumn();
		$expected = '45';
		$this->assertEquals($expected, $current);
	}
}