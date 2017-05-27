<?php

/**
* WizyTówka 5
* User — database object model.
*/
namespace WizyTowka;

class User extends DatabaseObject
{
	const PERM_CREATING_PAGES        = 0b00000001;
	const PERM_SENDING_FILES         = 0b00000010;
	const PERM_EDITING_OTHERS_PAGES  = 0b00000100;
	const PERM_EDITING_SITE_ELEMENTS = 0b00001000;
	const PERM_EDITING_SYSTEM_CONFIG = 0b00010000;
	const PERM_FILES_EDITOR_ACCESS   = 0b00100000;
	const PERM_SUPER_USER            = 0b01000000;

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