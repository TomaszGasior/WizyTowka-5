<?php

/**
* WizyTówka 5
* User — database object model.
*/
namespace WizyTowka;

class User extends DatabaseObject
{
	const CREATING_PAGES        = 0b00000001;
	const SENDING_FILES         = 0b00000010;
	const EDITING_OTHERS_PAGES  = 0b00000100;
	const EDITING_SITE_ELEMENTS = 0b00001000;
	const EDITING_SYSTEM_CONFIG = 0b00010000;
	const FILES_EDITOR_ACCESS   = 0b00100000;
	const SUPER_USER            = 0b01000000;

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