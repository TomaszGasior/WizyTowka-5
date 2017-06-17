<?php

/**
* WizyTówka 5
* PageBox — database object model.
*/
namespace WizyTowka;

class PageBox extends DatabaseObject
{
	static protected $_tableName = 'PageBoxes';
	static protected $_tableColumns = [
		'type',
		'contents',
		'settings',
		'pageId',
		'positionRow',
		'positionColumn',
	];
	static protected $_tableColumnsJSON = [
		'contents',
		'settings',
	];

	static public function getAll($pageId)
	{
		return static::_getByWhereCondition('pageId = :pageId', ['pageId' => $pageId]);
	}
	// This method overwrites DatabaseObject::getAll().
	// PageBox::getAll() returns page boxes only for page specified in first argument.
}