<?php

/**
* WizyTówka 5
* Language of pages — database object model.
*/
namespace WizyTowka;

class Language extends DatabaseObject
{
	static protected $_tableName = 'Languages';
	static protected $_tableColumns = [
		'name',
		'slug',
	];

	static public function getBySlug($slug)
	{
		return static::_getByWhereCondition('slug = :slug', ['slug' => $slug], true);
	}
}