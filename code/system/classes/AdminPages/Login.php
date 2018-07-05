<?php

/**
* WizyTówka 5
* Admin page — login form.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Login extends __\AdminPanelPage
{
	protected $_userMustBeLoggedIn = false;

	private $_session;

	protected function _prepare() : void
	{
		$this->_session = __\WT()->session;

		// Redirect user to default page of admin panel, when user is already logged in.
		if ($this->_session->isUserLoggedIn()) {
			$this->_redirect(null);
		}
	}

	public function POSTQuery() : void
	{
		if (!$_POST['name'] or !$_POST['password']) {
			$this->_HTMLMessage->error('Nie określono danych logowania.');
			return;
		}

		$user = __\User::getByName($_POST['name']);

		if ($user and $user->checkPassword($_POST['password'])) {
			$this->_session->logIn($user->id, 3600);

			// Update last login time statistics in database.
			$user->lastLoginTime = time();
			$user->save();

			$this->_redirectAfterLogIn();
		}
		else {
			$this->_HTMLMessage->error('Dane logowania są niepoprawne.');
		}
	}

	protected function _output() : void
	{
		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->default('Wylogowano się z panelu administracyjnego.');
		}
		if (!empty($_GET['redirect'])) {
			$this->_HTMLMessage->default('Po zalogowaniu nastąpi przekierowanie do właściwej strony.');
		}

		$this->_HTMLTemplate->lastUsername = empty($_POST['name']) ? '' : $_POST['name'];
	}

	private function _redirectAfterLogIn() : void
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