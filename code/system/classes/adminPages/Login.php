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
			$this->_redirect(WT\AdminPanel::URL(null));
		}
	}

	public function POSTQuery()
	{
		$this->_apMessageError = true;

		if (empty($_POST['name']) or empty($_POST['password'])) {
			$this->_apMessage = 'Nie podano danych logowania.';
			return;
		}

		$user = WT\User::getByName($_POST['name']);

		if ($user and $user->checkPassword($_POST['password'])) {
			WT\SessionManager::logIn($user->id, 3600);
			$this->_redirect(WT\AdminPanel::URL(null));
		}
		else {
			$this->_apMessage = 'Dane logowania są niepoprawne.';
		}
	}

	protected function _output()
	{
		$this->_apTemplate->lastUsername = (empty($_POST['name'])) ? '' : htmlspecialchars($_POST['name']);
		// Username will be shown, when user specify wrong password. It must be filtered by htmlspecialchars(),
		// because this form field is not filtered by Controller::filterPOSTData(). See "Login" form template.
	}
}