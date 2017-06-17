<?php

/**
* WizyTówka 5
* Admin page — system informations.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Informations extends WT\AdminPanel
{
	protected $_pageTitle = 'Informacje o systemie';

	protected function _output()
	{
		$this->_apTemplate->version = WT\VERSION;
		$this->_apTemplate->releaseDate = (new WT\Text(WT\VERSION_DATE))->formatAsDate()->get();
		$this->_apTemplate->betaVersionWarning = !WT\VERSION_STABLE;
	}
}