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
		$this->_tplMessage = 'Zmiany zostały zapisane.';
	}

	protected function _output()
	{
	}
}