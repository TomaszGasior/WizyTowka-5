<?php

/**
* WizyTówka 5
* Admin page — login form.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Login extends WT\AdminPanelPage
{
	protected $_userMustBeLoggedIn = false;

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
			$this->_redirectAfterLogIn();
		}
		else {
			$this->_HTMLMessage->error('Dane logowania są niepoprawne.');
		}
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_HTMLMessage->default('Wylogowano się z panelu administracyjnego.');
		}
		if (!empty($_GET['redirect'])) {
			$this->_HTMLMessage->default('Po zalogowaniu nastąpi przekierowanie do właściwej strony.');
		}

		$this->_HTMLTemplate->lastUsername = empty($_POST['name']) ? '' : $_POST['name'];
	}

	private function _redirectAfterLogIn()
	{
		if (!empty($_GET['redirect'])) {
			parse_str($_GET['redirect'], $arguments);

			if (!empty($arguments['c'])) {
				$target = $arguments['c'];
				unset($arguments['c']);
				$this->_redirect($target, $arguments);
			}
		}

		$this->_redirect(null);
	}
}