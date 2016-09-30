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
		switch ($driver) {
			case 'sqlite':
				self::$_pdo = new \PDO('sqlite:'.$database);
				break;
			case 'mysql':
			case 'pgsql':
				self::$_pdo = new \PDO($driver.':host='.$host.';dbname='.$database.';charset=utf8', $login, $password);
				break;
			default:
				throw new \Exception('Unsupported database type: ' . $driver . '.', 8);
				return;
		}

		self::$_pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		self::$_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	static public function pdo()
	{
		if (empty(self::$_pdo)) {
			throw new \Exception('Database connection was not established properly.', 9);
			return;
		}
		return self::$_pdo;
	}

	static public function executeSQL($sql)
	{
		return self::$_pdo->exec($sql);
	}
}