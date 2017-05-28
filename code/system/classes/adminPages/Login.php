<?php

/**
* WizyTówka 5
* Admin page — login form.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Login extends WT\AdminPanel
{
	protected $_apAlternateLayout = true;

	protected function _prepare()
	{
		if (WT\SessionManager::isUserLoggedIn()) {
			$this->_redirect(AdminPanel::URL('pages'));
		}
	}

	public function POSTQuery()
	{
	}

	protected function _output()
	{
	}
}