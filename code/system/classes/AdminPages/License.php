<?php

/**
* WizyTówka 5
* Admin page — license text.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class License extends __\AdminPanelPage
{
	protected $_pageTitle = 'Licencja';

	protected function _output() : void
	{
		// Load license text from main templates directory.
		// This file is shared with system installer.
		$this->_HTMLTemplate->setTemplatePath(__\SYSTEM_DIR . '/templates');
	}
}