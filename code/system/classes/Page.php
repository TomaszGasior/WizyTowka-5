<?php

/**
* WizyTówka 5
* Page — database object model.
*/
namespace WizyTowka;

class Page extends DatabaseObject
{
	static protected $_tableName = 'Pages';
	static protected $_tableColumns = [
		'slug',
		'contentType',
		'title',
		'titleMenu',
		'titleHead',
		'description',
		'contents',
		'settings',
		'userId',
		'languageId',
		'updatedTime',
		'createdTime',
	];
	static protected $_tableColumnsJSON = [
		'contents',
		'settings',
	];
	static protected $_tableColumnsTimeAtInsert = [
		'updatedTime',
		'createdTime',
	];
	static protected $_tableColumnsTimeAtUpdate = [
		'updatedTime',
	];

	static public function getBySlug($slug)
	{
		return static::_getByWhereCondition('slug = :slug', ['slug' => $slug], true);
	}

	static public function getByLanguageId($languageId)
	{
		return static::_getByWhereCondition('languageId = :languageId', ['languageId' => $languageId]);
	}
}