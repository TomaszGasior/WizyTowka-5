<?php

/**
* WizyTówka 5
* Abstract class of database object model.
*/
namespace WizyTowka;

abstract class Model implements \IteratorAggregate
{
	static protected $_tableName = '';
	static protected $_tablePrimaryKey = 'id';
	static protected $_tableColumns = [];    // Must not contain primary key.

	private $_data = [];
	private $_dataNewlyCreated;

	public function __construct() {
		$this->_data[static::$_tablePrimaryKey] = null;
		foreach (static::$_tableColumns as $column) {
			$this->_data[$column] = null;
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
			throw new \Exception('Primary key cannot be edited.', 7);
			return;
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
		return new ArrayIterator($this->_data);
	}

	public function save()
	{
		if ($this->_dataNewlyCreated) {
			$parameters = array_map(function($column){ return ':' . $column; }, static::$_tableColumns);
			$sqlQuery = 'INSERT INTO ' . static::$_tableName . '(' . implode(', ', static::$_tableColumns) . ') VALUES(' . implode(', ', $parameters) . ')';
		}
		else {
			$columnAndParameterAssignments = array_map(function($column){ return $column . ' = :' . $column; }, static::$_tableColumns);
			$sqlQuery = 'UPDATE ' . static::$_tableName . ' SET ' . implode(', ', $columnAndParameterAssignments) . ' WHERE ' . static::$_tablePrimaryKey . ' = ' . $this->_data[static::$_tablePrimaryKey];
		}

		$data = $this->_data;
		unset($data[static::$_tablePrimaryKey]);

		$statement = Database::pdo()->prepare($sqlQuery);
		$execution = $statement->execute($data);

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

		$sqlQuery = 'DELETE FROM ' . static::$_tableName . ' WHERE ' . static::$_tablePrimaryKey . ' = :id ';

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
		$sqlQuery = 'SELECT ' . static::$_tablePrimaryKey . ', ' . implode(', ', static::$_tableColumns) . ' FROM ' . static::$_tableName;
		if ($sqlQueryWhere) {
			$sqlQuery .= ' WHERE ' . $sqlQueryWhere;
		}

		$statement = Database::pdo()->prepare($sqlQuery);
		$statement->setFetchMode(\PDO::FETCH_ASSOC); // Assoc mode is needed because of…
		$execution = $statement->execute($parameters);

		$thisClassName = get_called_class();
		$elementsToReturn = [];

		if ($execution) {
			foreach ($statement as $record) {
				$object = new $thisClassName;
				$object->_data = $record;   // …assignment in this line.
				$object->_dataNewlyCreated = false;

				$elementsToReturn[] = $object;
			}
		}

		if ($mustBeOnlyOneRecord) {
			if (isset($elementsToReturn[0])) {
				if (isset($elementsToReturn[1])) {
					throw new \Exception('Database returned more than one record, when only one expected.', 10);
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