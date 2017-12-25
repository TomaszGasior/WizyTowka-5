<?php

/**
* WizyTówka 5
* User session manager.
*/
namespace WizyTowka;

trait SessionManager
{
	static private $_cookieName = 'WTCMSSession';

	static private $_started = false;
	static private $_currentUserId = false;

	static public function setup()
	{
		if (self::$_started) {
			throw SessionManagerException::alreadyStarted();
		}
		self::$_started = true;

		$sessionId = isset($_COOKIE[self::$_cookieName]) ? $_COOKIE[self::$_cookieName] : false;
		if ($sessionId) {
			$sessionsConfig = self::_getSessionsConfig();
			$session = isset($sessionsConfig->$sessionId) ? $sessionsConfig->$sessionId : false;

			if ($session and $session['waiString'] == self::_generateWAI($session['userId']) and time() < $session['expireTime']) {
				self::$_currentUserId = $session['userId'];

				// Periodically log out user and log in again to change session ID for better security.
				if (time() > $session['reloginTime']) {
					self::logOut();
					self::logIn($session['userId'], $session['expireTime'] - time());
					self::$_currentUserId = $session['userId'];
				}
			}
			// If session is expired or WAI string is incorrect, destroy session data such as when user is logged out.
			else {
				self::$_currentUserId = -1;  // This is a fake value. logOut() method needs it to work.
				self::logOut();
			}
		}
	}

	static public function logIn($userId, $sessionDuration)
	{
		if (!self::$_started or self::isUserLoggedIn()) {
			throw SessionManagerException::wrongState();
		}

		$session = [];

		$session['userId']      = $userId;
		$session['waiString']   = self::_generateWAI($userId);
		$session['expireTime']  = time() + (integer)$sessionDuration;
		$session['reloginTime'] = time() + 120;

		$sessionId = hash('sha512', random_int(PHP_INT_MIN, PHP_INT_MAX));
		$sessionsConfig = self::_getSessionsConfig();
		$sessionsConfig->$sessionId = $session;

		$forceHTTPS = (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off');
		setcookie(self::$_cookieName, $sessionId, $session['expireTime'], null, null, $forceHTTPS, true);

		// User will be logged in next request!
	}

	static public function logOut()
	{
		if (!self::$_started or !self::isUserLoggedIn()) {
			throw SessionManagerException::wrongState();
		}

		$sessionsConfig = self::_getSessionsConfig();
		$sessionId = $_COOKIE[self::$_cookieName];
		unset($sessionsConfig->$sessionId);

		setcookie(self::$_cookieName, null, 1);

		self::$_currentUserId = false;

		// Clean up expired sessions.
		foreach ($sessionsConfig as $sessionId => $session) {
			if (time() > $session['expireTime']) {
				unset($sessionsConfig->$sessionId);
			}
		}
	}

	static public function isUserLoggedIn()
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
			. ((!empty($_SERVER['HTTP_USER_AGENT']))      ? $_SERVER['HTTP_USER_AGENT']      : '')
			. ((!empty($_SERVER['HTTP_ACCEPT']))          ? $_SERVER['HTTP_ACCEPT']          : '')
			. ((!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '')
			. ((!empty($_SERVER['HTTP_HOST']))            ? $_SERVER['HTTP_HOST']            : '')
		);
	}

	static private function _getSessionsConfig()
	{
		static $conf = false;
		return ($conf) ? $conf : ($conf = new ConfigurationFile(CONFIG_DIR . '/sessions.conf'));
	}
}

class SessionManagerException extends Exception
{
	static public function alreadyStarted()
	{
		return new self('User session manager is already started.', 1);
	}
	static public function wrongState()
	{
		return new self('User session manager is not started yet or user session is in wrong state.', 2);
	}
}