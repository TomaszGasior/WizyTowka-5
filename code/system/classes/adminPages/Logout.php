<?php

/**
* WizyTówka 5
* Admin page — logout page.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Logout extends WT\AdminPanel
{
	protected function _prepare()
	{
		WT\SessionManager::logOut();

		$this->_redirect('login');
	}
}