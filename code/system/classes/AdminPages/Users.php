<?php

/**
* WizyTówka 5
* Admin page — files.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Users extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Użytkownicy';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	private $_users;

	protected function _prepare()
	{
		if (WT\Settings::get('lockdownUsers')) {
			$this->_redirect('error', ['type' => 'lockdown']);
		}

		if (!empty($_GET['deleteId'])) {
			$this->_deleteUser($_GET['deleteId']);
		}

		$this->_users = WT\User::getAll();
	}

	private function _deleteUser($userId)
	{
		// Important: user with PERM_SUPER_USER permission must not be deleted.

		if ($user = WT\User::getById($userId)) {
			if ($user->id == $this->_currentUser->id) {
				$this->_HTMLMessage->error('Nie można usunąć własnego konta użytkownika.');
			}
			elseif ($user->permissions & WT\User::PERM_SUPER_USER) {
				$this->_HTMLMessage->error('Nie można usunąć superużytkownika „%s”.', $user->name);
			}
			else {
				$user->delete();
				$this->_HTMLMessage->success('Konto użytkownika „%s” zostało usunięte.', $user->name);
			}
		}
	}

	protected function _output()
	{
		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->success('Konto użytkownika zostało utworzone.');
		}

		$this->_HTMLContextMenu->append('Utwórz użytkownika', self::URL('userCreate'), 'iconAdd');

		$this->_HTMLTemplate->users = $this->_users;
	}
}