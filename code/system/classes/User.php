<?php

/**
* WizyTówka 5
* User — database object model.
*/
namespace WizyTowka;

/**
* @property $id
* @property $name
* @property $password
* @property $email
* @property $permissions
* @property $lastLoginTime
* @property $createdTime
*/
class User extends DatabaseObject
{
	const PERM_CREATE_PAGES     = 0b00000001;
	const PERM_PUBLISH_PAGES    = 0b00000010;
	const PERM_EDIT_PAGES       = 0b00000100;
	const PERM_MANAGE_PAGES     = self::PERM_CREATE_PAGES | self::PERM_PUBLISH_PAGES | self::PERM_EDIT_PAGES;
	const PERM_MANAGE_FILES     = 0b00001000;
	const PERM_WEBSITE_ELEMENTS = 0b00010000;
	const PERM_WEBSITE_SETTINGS = 0b00100000;
	const PERM_SUPER_USER       = 0b01000000;

	static protected $_tableName = 'Users';
	static protected $_tableColumns = [
		'name',
		'password',
		'email',
		'permissions',
		'lastLoginTime',
		'createdTime',
	];
	static protected $_tableColumnsTimeAtInsert = [
		'createdTime',
	];

	static public function getByName($name) : ?self
	{
		return static::_getByWhereCondition('name = :name', ['name' => $name], true);
	}

	public function setPassword(string $givenPassword) : void
	{
		$this->password = password_hash($givenPassword, PASSWORD_BCRYPT, ['cost' => 13]);
	}

	public function checkPassword(string $givenPassword) : bool
	{
		return password_verify($givenPassword, $this->password);
	}
}