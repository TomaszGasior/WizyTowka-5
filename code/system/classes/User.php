<?php

/**
* WizyTówka 5
* User — database object model.
*/
namespace WizyTowka;

class User extends DatabaseObject
{
	static protected $_tableName = 'Users';
	static protected $_tableColumns = [
		'name',
		'password',
		'createdTime',
	];
	static protected $_tableColumnsTimeAtInsert = [
		'createdTime',
	];

	static public function getByName($name)
	{
		return static::_getByWhereCondition('name = :name', ['name' => $name], true);
	}
}