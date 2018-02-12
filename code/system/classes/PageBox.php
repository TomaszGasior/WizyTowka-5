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
		'contentType',
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

	// This method overwrites DatabaseObject::getAll().
	// PageBox::getAll() returns page boxes only for page specified in first argument.
	static public function getAll($pageId = null)
	{
		return $pageId ? static::_getByWhereCondition('pageId = :pageId', ['pageId' => $pageId]) : [];
	}
}