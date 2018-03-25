<?php

/**
* WizyTówka 5
* Admin page — user preferences.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Preferences extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Preferencje';

	protected function _prepare()
	{
		if (isset($_GET['closeOtherSessions'])) {
			$sessionsWereClosed = WT\SessionManager::closeOtherSessions();
			self::_redirect('preferences', ['msg' => $sessionsWereClosed ? 2 : 1]);
		}
	}

	public function POSTQuery()
	{
		if ($_POST['currentPassword'] and $_POST['passwordText_1'] and $_POST['passwordText_2']) {
			if (!$this->_currentUser->checkPassword($_POST['currentPassword'])) {
				$this->_HTMLMessage->error('Podane aktualne hasło jest niepoprawne. Hasło nie zostało zmienione.');
			}
		    elseif ($_POST['passwordText_1'] === $_POST['passwordText_2']) {
				$this->_currentUser->setPassword($_POST['passwordText_1']);
				$this->_currentUser->save();

				$this->_HTMLMessage->success('Hasło zostało zmienione.');
			}
			else {
				$this->_HTMLMessage->error('Podane hasła nie są identyczne. Hasło nie zostało zmienione.');
			}
		}
	}

	protected function _output()
	{
		$this->_HTMLContextMenu->append('Wyloguj inne sesje', self::URL('preferences', ['closeOtherSessions' => 1]), 'iconLogout');

		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->success(
				$_GET['msg'] == 2 ? 'Inne sesje twojego konta użytkownika zostały wylogowane.'
				                  : 'Nie istnieją żadne inne sesje twojego konta użytkownika.'
			);
		}
	}
}