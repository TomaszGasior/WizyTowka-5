<?php

/**
* WizyTówka 5
* Admin page — user editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class UserEdit extends __\AdminPanelPage
{
	use UserEditCreateCommon;

	protected $_pageTitle = 'Edycja użytkownika';
	protected $_userRequiredPermissions = __\User::PERM_SUPER_USER;

	private $_user;

	public function _prepare()
	{
		if (__\WT()->settings->lockdownUsers) {
			$this->_redirect('error', ['type' => 'lockdown']);
		}

		if (empty($_GET['id']) or !$this->_user = __\User::getById($_GET['id'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}
	}

	public function POSTQuery()
	{
		if ($_POST['name'] and $this->_user->name != $_POST['name']) {
			if (!$this->_checkUserName($_POST['name'])) {
				$this->_HTMLMessage->error('Nazwa użytkownika jest niepoprawna. Nie zmieniono nazwy użytkownika.');
			}
			elseif (__\User::getByName($_POST['name'])) {
				$this->_HTMLMessage->error('Nazwa użytkownika „%s” jest zajęta.', $_POST['name']);
			}
			else {
				$this->_user->name = $_POST['name'];
			}
		}

		if (!$_POST['email'] or filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$this->_user->email = $_POST['email'];
		}
		else {
			$this->_HTMLMessage->error('Podany adres e-mail jest niepoprawny.');
		}

		if ($_POST['passwordText_1'] and $_POST['passwordText_2']) {
		    if ($_POST['passwordText_1'] === $_POST['passwordText_2']) {
				$this->_user->setPassword($_POST['passwordText_1']);
				$this->_HTMLMessage->success('Hasło zostało zmienione.');
			}
			else {
				$this->_HTMLMessage->error('Podane hasła nie są identyczne. Hasło nie zostało zmienione.');
			}
		}

		$permissions = $this->_calculatePermissionValueFromNamesArray(isset($_POST['permissions']) ? $_POST['permissions'] : []);
		if ($this->_user->id == $this->_currentUser->id and !($permissions & __\User::PERM_SUPER_USER)) {
			$this->_HTMLMessage->error('Nie można odebrać samemu sobie uprawnień superużytkownika.');
		}
		else {
			$this->_user->permissions = $permissions;
		}

		$this->_user->save();
		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('UserEditCreate');
		$this->_HTMLTemplate->createInsteadEdit = false;

		$this->_HTMLTemplate->user        = $this->_user;
		$this->_HTMLTemplate->permissions = $this->_prepareNamesArrayFromPermissionValue($this->_user->permissions);
	}
}