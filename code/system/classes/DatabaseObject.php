<?php

/**
* WizyTówka 5
* Abstract class of database object.
*/
namespace WizyTowka;

abstract class DatabaseObject implements \IteratorAggregate
{
	static protected $_tableName = '';
	static protected $_tablePrimaryKey = 'id';
	static protected $_tableColumns = [];    // Must not contain primary key.

	static protected $_tableColumnsJSON = [];
	static protected $_tableColumnsTimeAtInsert = [];
	static protected $_tableColumnsTimeAtUpdate = [];

	private $_data = [];
	private $_dataNewlyCreated;

	public function __construct() {
		$this->_data[static::$_tablePrimaryKey] = null;
		foreach (static::$_tableColumns as $column) {
			$this->_data[$column] = (in_array($column, static::$_tableColumnsJSON)) ? new \stdClass : null;
		}

		$this->_dataNewlyCreated = true;
	}

	public function __get($column)
	{
		return $this->_data[$column];
	}

	public function __set($column, $value)
	{
		if ($column == static::$_tablePrimaryKey) {
			throw new WTException('Primary key cannot be edited.', 10);
		}
		elseif (in_array($column, static::$_tableColumnsJSON) and !is_object($value)) {
			throw new WTException('JSON object cannot be replaced by non-object value.', 12);
		}

		$this->_data[$column] = $value;
	}

	public function __isset($column)
	{
		return isset($this->_data[$column]);
	}

	public function __debugInfo() // For var_dump() since PHP 5.6.
	{
		return $this->_data;
	}

	public function getIterator() // For IteratorAggregate interface.
	{
		return new \ArrayIterator($this->_data);
	}

	public function save()
	{
		if ($this->_dataNewlyCreated) {
			foreach (static::$_tableColumnsTimeAtInsert as $column) {
				$this->_data[$column] = time();
			}

			$parameters = array_map(function($column){ return ':' . $column; }, static::$_tableColumns);
			$sqlQuery = 'INSERT INTO ' . static::$_tableName . '(' . implode(', ', static::$_tableColumns) . ') VALUES(' . implode(', ', $parameters) . ')';
		}
		else {
			foreach (static::$_tableColumnsTimeAtUpdate as $column) {
				$this->_data[$column] = time();
			}

			$columnAndParameterAssignments = array_map(function($column){ return $column . ' = :' . $column; }, static::$_tableColumns);
			$sqlQuery = 'UPDATE ' . static::$_tableName . ' SET ' . implode(', ', $columnAndParameterAssignments) . ' WHERE ' . static::$_tablePrimaryKey . ' = :' . static::$_tablePrimaryKey;
		}

		$sqlParameters = $this->_data;

		if ($this->_dataNewlyCreated) {
			unset($sqlParameters[static::$_tablePrimaryKey]);  // Remove primary key from INSERT query parameters.
		}
		foreach (static::$_tableColumnsJSON as $column) {
			$sqlParameters[$column] = json_encode($sqlParameters[$column], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
			if (json_last_error() != JSON_ERROR_NONE) {
				throw new WTException('Error during writing JSON string: ' . json_last_error_msg() . '.', 14);
			}
		}

		$statement = Database::pdo()->prepare($sqlQuery);
		$execution = $statement->execute($sqlParameters);

		if ($execution and $this->_dataNewlyCreated) {
			$this->_data[static::$_tablePrimaryKey] = Database::pdo()->lastInsertId(static::$_tablePrimaryKey);
			$this->_dataNewlyCreated = false;
		}

		$statement->closeCursor();

		return $execution;
	}

	public function delete()
	{
		if ($this->_dataNewlyCreated) {
			return;
		}

		$sqlQuery = 'DELETE FROM ' . static::$_tableName . ' WHERE ' . static::$_tablePrimaryKey . ' = :id';

		$statement = Database::pdo()->prepare($sqlQuery);
		$execution = $statement->execute(['id' => $this->_data[static::$_tablePrimaryKey]]);

		if ($execution) {
			$this->_data[static::$_tablePrimaryKey] = null;
			$this->_dataNewlyCreated = true;
		}

		$statement->closeCursor();

		return $execution;
	}

	static protected function _getByWhereCondition($sqlQueryWhere = null, array $parameters = [], $mustBeOnlyOneRecord = false)
	{
		$allColumnsNames = static::$_tableColumns;
		array_unshift($allColumnsNames, static::$_tablePrimaryKey);  // With primary key column.

		$sqlQuery = 'SELECT ' . implode(', ', $allColumnsNames) . ' FROM ' . static::$_tableName;
		if ($sqlQueryWhere) {
			$sqlQuery .= ' WHERE ' . $sqlQueryWhere;
		}
		$sqlQuery .= ' ORDER BY ' . static::$_tablePrimaryKey;

		$statement = Database::pdo()->prepare($sqlQuery);
		$statement->setFetchMode(\PDO::FETCH_NUM);   // Values must not be duplicated.
		$execution = $statement->execute($parameters);

		$thisClassName = get_called_class();
		$elementsToReturn = [];

		if ($execution) {
			foreach ($statement as $record) {
				$object = new $thisClassName;
				$object->_dataNewlyCreated = false;
				$object->_data = array_combine($allColumnsNames, $record);
				// Normally it is possible to use PDO::FETCH_NAMED fetch mode and $object->_data = $record syntax, but this is PostgreSQL workaround.
				// By default PostgreSQL lowercases names of columns and tables — this makes camelCase columns names inaccesible.

				foreach (static::$_tableColumnsJSON as $column) {
					if ($object->_data[$column] == '') {
						$object->_data[$column] = new \stdClass;
					}
					else {
						$object->_data[$column] = json_decode($object->_data[$column]);
						if (json_last_error() != JSON_ERROR_NONE) {
							throw new WTException('Error during reading JSON string: ' . json_last_error_msg() . '.', 13);
						}
					}
				}

				$elementsToReturn[] = $object;
			}
		}

		if ($mustBeOnlyOneRecord) {
			if (isset($elementsToReturn[0])) {
				if (isset($elementsToReturn[1])) {
					throw new WTException('Database returned more than one record, when only one expected.', 11);
				}
				return $elementsToReturn[0];
			}
			else
				return false;
		}

		return $elementsToReturn;
	}

	static public function getById($id)
	{
		// This method uses table primary key. It is "id" by default.
		return static::_getByWhereCondition(static::$_tablePrimaryKey.' = :id', ['id' => $id], true);
	}

	static public function getAll()
	{
		return static::_getByWhereCondition();
	}
}