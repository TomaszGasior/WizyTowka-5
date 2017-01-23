<?php

/**
* WizyTówka 5
* Admin page — pages.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Pages extends WT\AdminPanel
{
	protected $_pageTitle = 'Strony';

	private $_pages;

	protected function _prepare()
	{
		$this->_pages = WT\Page::getAll();
	}

	protected function _output()
	{
		$this->_apTemplate->pages = $this->_pages;
	}
}