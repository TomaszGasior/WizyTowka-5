<?php

/**
* WizyTówka 5
* Admin page — about system.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class About extends __\AdminPanelPage
{
	protected $_pageTitle = 'Informacje o systemie';

	protected function _output()
	{
		$this->_HTMLTemplate->version            = __\VERSION;
		$this->_HTMLTemplate->releaseDate        = __\VERSION_DATE;
		$this->_HTMLTemplate->betaVersionWarning = !__\VERSION_STABLE;
	}
}