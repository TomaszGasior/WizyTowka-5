<?php

/**
* WizyTówka 5
* User session manager.
*/
namespace WizyTowka;

class UserSession
{
	static private $_cookieName = 'WTCMSSession';

	static private $_started = false;
	static private $_currentUserId = false;

	static public function setup()
	{
		if (self::$_started) {
			throw new Exception('User session manager is already started.', 16);
		}
		self::$_started = true;

		$sessionId = (isset($_COOKIE[self::$_cookieName])) ? $_COOKIE[self::$_cookieName] : false;
		if ($sessionId) {
			$sessionsConfig = self::_getSessionsConfig();
			$session = (isset($sessionsConfig->$sessionId)) ? $sessionsConfig->$sessionId : false;

			if ($session and $session['waiString'] == self::_generateWAI($session['userId']) and time() < $session['expireTime']) {
				self::$_currentUserId = $session['userId'];
			}
			else {
				// If session is expired or WAI string is not correct, destroy session data such as when user is logged out.
				self::$_currentUserId = -1;  // This is a fake value. logOut() method needs it to work.
				self::logOut();
			}
		}
	}

	static public function logIn($userId, $sessionDuration)
	{
		if (!self::$_started or self::isLoggedIn()) {
			throw new Exception('User session manager cannot log in user.', 17);
		}

		$session['userId'] = $userId;
		$session['waiString'] = self::_generateWAI($userId);
		$session['expireTime'] = time() + (integer)$sessionDuration;

		$sessionId = hash('sha512', uniqid(1));
		$sessionsConfig = self::_getSessionsConfig();
		$sessionsConfig->$sessionId = $session;

		setcookie(self::$_cookieName, $sessionId, $session['expireTime']);

		self::$_currentUserId = $userId;
	}

	static public function logOut()
	{
		if (!self::$_started or !self::isLoggedIn()) {
			throw new Exception('User session manager cannot log out user.', 18);
		}

		$sessionsConfig = self::_getSessionsConfig();
		$sessionId = $_COOKIE[self::$_cookieName];
		unset($sessionsConfig->$sessionId);

		setcookie(self::$_cookieName, null, 1);

		self::$_currentUserId = false;
	}

	static public function isLoggedIn()
	{
		return (self::$_currentUserId) ? true : false;
	}

	static public function getUserId()
	{
		return (self::$_currentUserId) ? self::$_currentUserId : false;
	}

	static private function _generateWAI($userId) // WAI means "where am I?". This string is used to identify user agent.
	{
		return hash('sha512',
			$userId . $_SERVER['REMOTE_ADDR']
			. ((!empty($_SERVER['HTTP_ACCEPT_ENCODING'])) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '')
			. ((!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '')
			. ((!empty($_SERVER['HTTP_ACCEPT']))          ? $_SERVER['HTTP_ACCEPT']          : '')
			. ((!empty($_SERVER['HTTP_USER_AGENT']))      ? $_SERVER['HTTP_USER_AGENT']      : '')
		);
	}

	static private function _getSessionsConfig()
	{
		return new ConfigurationFile(CONFIG_DIR.'/sessions.conf');
	}
}