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
		if (!empty($_GET['deleteId']) and $user = WT\User::getById($_GET['deleteId'])) {
			if ($user->id == 1) {
				$this->_HTMLMessage->error('Użytkownik „' . $user->name . '” nie może zostać usunięty.');
			}
			elseif ($user->id == $this->_currentUser->id) {
				$this->_HTMLMessage->error('Nie można usunąć własnego konta użytkownika.');
			}
			else {
				$user->delete();
				$this->_HTMLMessage->success('Użytkownik „' . $user->name . '” został usunięty.');
			}
		}

		$this->_users = WT\User::getAll();
	}

	protected function _output()
	{
		$this->_HTMLTemplate->users = $this->_users;
	}
}