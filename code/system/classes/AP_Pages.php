<?php

/**
* WizyTówka 5
* Admin panel — pages.
*/
namespace WizyTowka;

class AP_Pages extends AdminPanel
{
	protected $_pageTitle = 'Strony';

	protected function _prepare()
	{
	}

	public function POSTQuery()
	{
		$this->_apMessage = 'Zmiany zostały zapisane.';
	}

	protected function _output()
	{
		$this->_apContextMenu->add('Edycja', '#', 'iEdit');
		$this->_apContextMenu->add('Ustawienia', '#', 'iSettings');
	}
}