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
		'title',
		'titleHead',
		'description',
		'keywords',
		'isDraft',
		'userId',
		'updatedTime',
		'createdTime',
	];
	static protected $_tableColumnsTimeAtInsert = [
		'updatedTime',
		'createdTime',
	];
	static protected $_tableColumnsTimeAtUpdate = [
		'updatedTime',
	];

	// This method overwrites DatabaseObject::getAll().
	// Page::getAll() returns public pages, Page::getAllDrafts() returns pages with draft status.
	static public function getAll()
	{
		return static::_getByWhereCondition('isDraft = 0');
	}

	static public function getAllDrafts()
	{
		return static::_getByWhereCondition('isDraft = 1');
	}

	static public function getBySlug($slug)
	{
		return static::_getByWhereCondition('slug = :slug', ['slug' => $slug], true);
	}
}