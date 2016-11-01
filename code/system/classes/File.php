<?php

/**
* WizyTówka 5
* File — database object model.
*/
namespace WizyTowka;

class File extends DatabaseObject
{
	static protected $_tableName = 'Files';
	static protected $_tableColumns = [
		'name',
		'userId',
		'uploadedTime',
	];
	static protected $_tableColumnsTimeAtInsert = [
		'uploadedTime',
	];
}