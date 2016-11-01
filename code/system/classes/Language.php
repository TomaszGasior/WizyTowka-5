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
}