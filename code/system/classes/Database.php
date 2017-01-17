<?php

/**
* WizyTówka 5
* Database connection manager.
*/
namespace WizyTowka;

trait Database
{
	static private $_pdo;

	static public function connect($driver, $database, $host = null, $login = null, $password = null)
	{
		if (!empty(self::$_pdo)) {
			throw DatabaseException::connectionAlreadyStarted();
		}

		switch ($driver) {
			case 'sqlite':
				self::$_pdo = new \PDO('sqlite:'.$database);
				break;
			case 'mysql':
				self::$_pdo = new \PDO('mysql:host='.$host.';dbname='.$database.';charset=utf8', $login, $password);
				break;
			case 'pgsql':
				self::$_pdo = new \PDO('pgsql:host='.$host.';dbname='.$database, $login, $password);
				break;
			default:
				throw DatabaseException::unsupportedDriver($driver);
		}

		self::$_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	static public function disconnect()
	{
		self::$_pdo = null;
	}

	static public function pdo()
	{
		if (empty(self::$_pdo)) {
			throw DatabaseException::connectionNotStartedYet();
		}
		return self::$_pdo;
	}

	static public function executeSQL($sql)
	{
		return self::pdo()->exec($sql);
	}
}

class DatabaseException extends Exception
{
	static public function connectionNotStartedYet()
	{
		return new self('Database connection was not established properly.', 1);
	}
	static public function connectionAlreadyStarted()
	{
		return new self('Database connection is already started.', 2);
	}
	static public function unsupportedDriver($driver)
	{
		return new self('Unsupported database type: ' . $driver . '.', 3);
	}
}