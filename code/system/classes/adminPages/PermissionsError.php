<?php

/**
* WizyTówka 5
* Admin page — permissions error page. User is redirected to this page, when he have not required permissions.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PermissionsError extends WT\AdminPanel
{
	protected $_pageTitle = 'Brak uprawnień';

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iWarning';
		$this->_apTemplate->messageText = 'Nie posiadasz wystarczających uprawnień do korzystania z&nbsp;tej strony panelu administracyjnego.';
	}
}