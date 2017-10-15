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
	protected $_apAlternateLayout = true;

	protected function _prepare()
	{
		if (WT\SessionManager::isUserLoggedIn()) {
			$this->_redirect(null);
		}
	}

	public function POSTQuery()
	{
		if (empty($_POST['name']) or empty($_POST['password'])) {
			$this->_apMessage->error('Nie podano danych logowania.');
			return;
		}

		$user = WT\User::getByName($_POST['name']);

		if ($user and $user->checkPassword($_POST['password'])) {
			WT\SessionManager::logIn($user->id, 3600);
			$this->_redirect(null);
		}
		else {
			$this->_apMessage->error('Dane logowania są niepoprawne.');
		}
	}

	protected function _output()
	{
		// Username will be shown, when user specify wrong password. It must be filtered by htmlspecialchars(),
		// because this form field is not filtered by Controller::filterPOSTData(). See "Login" form template.
		$this->_apTemplate->lastUsername = (empty($_POST['name'])) ? '' : htmlspecialchars($_POST['name']);
	}
}