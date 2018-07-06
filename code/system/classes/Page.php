<?php

/**
* WizyTówka 5
* Page — database object model.
*/
namespace WizyTowka;

/**
* @property $id
* @property $slug
* @property $title
* @property $titleHead
* @property $description
* @property $noIndex
* @property $isDraft
* @property $contentType
* @property $contents
* @property $settings
* @property $userId
* @property $updatedTime
* @property $createdTime
*/
class Page extends DatabaseObject
{
	static protected $_tableName = 'Pages';
	static protected $_tableColumns = [
		'slug',
		'title',
		'titleHead',
		'description',
		'noIndex',
		'isDraft',
		'contentType',
		'contents',
		'settings',
		'userId',
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

	// This method overwrites DatabaseObject::getAll().
	// Page::getAll() returns public pages, Page::getAllDrafts() returns pages with draft status.
	static public function getAll() : array
	{
		return static::_getByWhereCondition('isDraft = 0');
	}

	static public function getAllDrafts() : array
	{
		return static::_getByWhereCondition('isDraft = 1');
	}

	static public function getBySlug(string $slug) : ?self
	{
		return static::_getByWhereCondition('slug = :slug', ['slug' => $slug], true);
	}
}