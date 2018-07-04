<?php

/**
* WizyTówka 5
* PDO object wrapper with more convenient constructor.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class DatabasePDO extends \PDO
{
	public function __construct(string $driver, string $database, ?string $host = null, ?string $login = null, ?string $password = null)
	{
		switch ($driver) {
			case 'sqlite':
				parent::__construct('sqlite:' . $database);
				$this->exec('PRAGMA foreign_keys = ON'); // Constraints are disabled by default.
				break;
			case 'mysql':
				parent::__construct('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8mb4', $login, $password);
				$this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
				break;
			case 'pgsql':
				parent::__construct('pgsql:host=' . $host . ';dbname=' . $database, $login, $password);
				break;
			default:
				throw DatabasePDOException::unsupportedDriver($driver);
		}

		$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
}

class DatabasePDOException extends __\Exception
{
	static public function unsupportedDriver($driver)
	{
		return new self('Unsupported database type: "' . $driver . '".', 1);
	}
}