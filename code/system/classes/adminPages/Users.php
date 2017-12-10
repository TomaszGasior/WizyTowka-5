<?php

/**
* WizyTówka 5
* Admin page — files.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Users extends WT\AdminPanel
{
	protected $_pageTitle = 'Użytkownicy';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	private $_users;

	protected function _prepare()
	{
		// Important: user with PERM_SUPER_USER permission must not be deleted.

		if (!empty($_GET['deleteId']) and $user = WT\User::getById($_GET['deleteId'])) {
			if ($user->id == $this->_currentUser->id) {
				$this->_HTMLMessage->error('Nie można usunąć własnego konta użytkownika.');
			}
			elseif ($user->permissions & WT\User::PERM_SUPER_USER) {
				$this->_HTMLMessage->error('Nie można usunąć super użytkownika „' . $user->name . '”.');
			}
			else {
				$user->delete();
				$this->_HTMLMessage->success('Konto użytkownika „' . $user->name . '” zostało usunięte.');
			}
		}

		$this->_users = WT\User::getAll();
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_HTMLMessage->success('Konto użytkownika zostało utworzone.');
		}

		$this->_HTMLTemplate->users = $this->_users;
	}
}