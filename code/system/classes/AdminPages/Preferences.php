<?php

/**
* WizyTówka 5
* Admin page — user preferences.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Preferences extends __\AdminPanelPage
{
	protected $_pageTitle = 'Preferencje';

	protected function _prepare() : void
	{
		if (isset($_GET['closeOtherSessions'])) {
			$this->_closeOtherUserSessions();
		}
	}

	private function _closeOtherUserSessions()
	{
		$sessionsWereClosed = __\WT()->session->closeOtherSessions();

		$this->_HTMLMessage->success(
			$sessionsWereClosed ? 'Inne sesje twojego konta użytkownika zostały wylogowane.'
			                    : 'Nie istnieją żadne inne sesje twojego konta użytkownika.'
		);
		self::_redirect('preferences');
	}

	public function POSTQuery() : void
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

	protected function _output() : void
	{
		$this->_HTMLContextMenu->append('Wyloguj inne sesje', self::URL('preferences', ['closeOtherSessions' => 1]), 'iconLogout');
	}
}