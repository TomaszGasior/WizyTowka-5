<?php

/**
* WizyTówka 5
* Database connection manager.
*/
namespace WizyTowka;

class Database
{
	static private $_pdo;

	static public function connect($driver, $database, $host = null, $login = null, $password = null)
	{
		if (!empty(self::$_pdo)) {
			throw new WTException('Database connection is already started.', 7);
			return;
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
				throw new WTException('Unsupported database type: ' . $driver . '.', 8);
				return;
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
			throw new WTException('Database connection was not established properly.', 9);
			return;
		}
		return self::$_pdo;
	}

	static public function executeSQL($sql)
	{
		return self::$_pdo->exec($sql);
	}
}