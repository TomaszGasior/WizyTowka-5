<?php

/**
* WizyTówka 5
* Admin page — login form.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Login extends WT\AdminPanel
{
	protected $_userMustBeLoggedIn = false;
	protected $_alternativeLayout = true;

	protected function _prepare()
	{
		// Redirect user to default page of admin panel, when user is already logged in.
		if (WT\SessionManager::isUserLoggedIn()) {
			$this->_redirect(null);
		}
	}

	public function POSTQuery()
	{
		if (empty($_POST['name']) or empty($_POST['password'])) {
			$this->_HTMLMessage->error('Nie podano danych logowania.');
			return;
		}

		$user = WT\User::getByName($_POST['name']);

		if ($user and $user->checkPassword($_POST['password'])) {
			WT\SessionManager::logIn($user->id, 3600);
			$this->_redirect(null);
		}
		else {
			$this->_HTMLMessage->error('Dane logowania są niepoprawne.');
		}
	}

	protected function _output()
	{
		$this->_HTMLTemplate->lastUsername = empty($_POST['name']) ? '' : $_POST['name'];
	}
}