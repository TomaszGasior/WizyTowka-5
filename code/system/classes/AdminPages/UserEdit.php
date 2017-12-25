<?php

/**
* WizyTówka 5
* Admin page — user editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class UserEdit extends WT\AdminPanelPage
{
	use UserEditCreateCommon;

	protected $_pageTitle = 'Edycja użytkownika';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	private $_user;

	public function _prepare()
	{
		if (empty($_GET['id']) or !$this->_user = WT\User::getById($_GET['id'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}
	}

	public function POSTQuery()
	{
		if (!empty($_POST['name']) and $this->_user->name != $_POST['name']) {
			if (!$this->_checkUserName($_POST['name'])) {
				$this->_HTMLMessage->error('Nazwa użytkownika jest niepoprawna. Nie zmieniono nazwy użytkownika.');
			}
			elseif (WT\User::getByName($_POST['name'])) {
				$this->_HTMLMessage->error('Nazwa użytkownika „' . $_POST['name'] . '” jest zajęta.');
			}
			else {
				$this->_user->name = $_POST['name'];
			}
		}

		if (!empty($_POST['passwordText_1']) and !empty($_POST['passwordText_2'])) {
		    if ($_POST['passwordText_1'] === $_POST['passwordText_2']) {
				$this->_user->setPassword($_POST['passwordText_1']);
				$this->_HTMLMessage->success('Hasło zostało zmienione.');
			}
			else {
				$this->_HTMLMessage->error('Podane hasła nie są identyczne. Hasło nie zostało zmienione.');
			}
		}

		$permissions = $this->_calculatePermissionValueFromNamesArray(isset($_POST['permissions']) ? $_POST['permissions'] : []);
		if ($this->_user->id == $this->_currentUser->id and !($permissions & WT\User::PERM_SUPER_USER)) {
			$this->_HTMLMessage->error('Nie można odebrać samemu sobie uprawnień super użytkownika.');
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