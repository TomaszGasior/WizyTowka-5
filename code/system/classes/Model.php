<?php

/**
* WizyTówka 5
* Abstract class of database object model.
*/
namespace WizyTowka;

abstract class Model implements \IteratorAggregate
{
	static private $_tableName = '';
	static private $_tablePrimaryKey = 'id';
	static private $_tableColumns = [];    // Must not contain primary key.

	private $_data = [];
	private $_dataNotSavedYet = false;

	public function __construct() {
		foreach (static::$_tableColumns as $column) {
			$this->_data[$column] = null;
		}
		$this->_data[static::$_tablePrimaryKey] = null;

		$this->_dataNotSavedYet = true;
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
		if ($this->_dataNotSavedYet) {
			$parameters = array_map(function($column){ return ':' . $column; }, static::$_tableColumns);
			$sqlQuery = 'INSERT INTO ' . static::$_tableName . '(' . implode(', ', static::$_tableColumns) . ') VALUES(' . implode(', ', $parameters) . ')';
		}
		else {
			$columnAndParameterAssignments = array_map(function($column){ return $column . ' = :' . $column; }, static::$_tableColumns);
			$sqlQuery = 'UPDATE ' . static::$_tableName . ' SET ' . implode(', ', $columnAndParameterAssignments) . 'WHERE ' . static::$_tablePrimaryKey . ' = ' . $this->_data[static::$_tablePrimaryKey];
		}

		$statement = Database::pdo()->prepare($sqlQuery);
		$execution = $statement->execute($this->_data);

		if ($execution and $this->_dataNotSavedYet) {
			$this->_data[static::$_tablePrimaryKey] = Database::pdo()->lastInsertId(static::$_tablePrimaryKey);
			$this->_dataNotSavedYet = false;
		}

		$statement->closeCursor();

		return $execution;
	}

	public function delete()
	{
		if ($this->_dataNotSavedYet) {
			return;
		}

		$sqlQuery = 'DELETE FROM ' . static::$_tableName . ' WHERE ' . static::$_tablePrimaryKey . ' = ' . $this->_data[static::$_tablePrimaryKey];
		$execution = Database::pdo()->exec($sqlQuery);

		return (boolean)$execution;
	}

	static protected function getByWhereCondition($sqlQueryWhere = null, array $parameters = [])
	{
		$sqlQuery = 'SELECT ' . static::$_tablePrimaryKey . ', ' . implode(static::$_tableColumns) . ' FROM ' . static::$_tableName;
		if ($sqlQueryWhere) {
			$sqlQuery .= ' WHERE ' . $sqlQueryWhere;
		}

		$statement = Database::pdo()->prepare($sqlQuery);
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$execution = $statement->execute($parameters);

		$thisClassName = __CLASS__;
		$elements = [];

		if ($execution) {
			foreach ($statement as $record) {
				$object = new $thisClassName;
				$object->_data = $record;
				$elements[] = $object;
			}
		}

		return $elements;
	}

	static public function getById($id)
	{
		return static::getByWhereCondition('id = :id', ['id' => $id])[0];
	}

	static public function getAll()
	{
		return static::getByWhereCondition();
	}
}