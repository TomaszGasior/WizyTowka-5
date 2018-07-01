<?php

/**
* WizyTówka 5
* Admin page — logout page.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Logout extends __\AdminPanelPage
{
	private $_session;

	protected function _prepare()
	{
		$this->_session = __\WT()->session;

		$this->_session->logOut();

		$this->_redirect('login', ['msg' => 1]);
	}
}