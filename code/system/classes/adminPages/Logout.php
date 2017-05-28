<?php

/**
* WizyTówka 5
* Admin page — login form.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Logout extends WT\AdminPanel
{
	protected function _prepare()
	{
		WT\SessionManager::logOut();
		$this->_redirect(WT\AdminPanel::URL('login'));
	}

	protected function _output()
	{
		exit;
	}
}