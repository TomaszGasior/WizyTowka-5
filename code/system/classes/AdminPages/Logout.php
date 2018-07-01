<?php

/**
* WizyTówka 5
* Admin page — logout page.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Logout extends WT\AdminPanelPage
{
	private $_session;

	protected function _prepare()
	{
		$this->_session = WT\WT()->session;

		$this->_session->logOut();

		$this->_redirect('login', ['msg' => 1]);
	}
}