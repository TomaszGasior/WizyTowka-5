<?php

/**
* WizyTówka 5
* User session manager.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class SessionManager
{
	private $_cookieName;
	private $_sessionsConfig;
	private $_currentUserId = false;

	public function __construct($cookieName, __\ConfigurationFile $config)
	{
		$this->_cookieName     = $cookieName;
		$this->_sessionsConfig = $config;

		$sessionId = isset($_COOKIE[$this->_cookieName]) ? $_COOKIE[$this->_cookieName] : false;
		if ($sessionId) {
			$session = isset($this->_sessionsConfig->$sessionId) ? $this->_sessionsConfig->$sessionId : false;

			if ($session and $session['waiString'] == $this->_generateWAI($session['userId']) and time() < $session['expireTime']) {
				$this->_currentUserId = $session['userId'];

				// Periodically log out user and log in again to change session ID for better security.
				if (time() > $session['reloginTime']) {
					$this->logOut();
					$this->logIn($session['userId'], $session['expireTime'] - time());
					$this->_currentUserId = $session['userId'];
				}
			}
			// If session is expired or WAI string is incorrect, destroy session data such as when user is logged out.
			else {
				$this->_currentUserId = -1;  // This is a fake value. logOut() method needs it to work.
				$this->logOut();
			}
		}
	}

	public function logIn($userId, $sessionDuration)
	{
		if ($this->isUserLoggedIn()) {
			throw SessionManagerException::alreadyUserLoggedIn($this->_currentUserId);
		}

		$session = [];

		$session['userId']      = $userId;
		$session['waiString']   = $this->_generateWAI($userId);
		$session['expireTime']  = time() + (integer)$sessionDuration;  // Unix timestamp.
		$session['reloginTime'] = time() + 120;

		$sessionId = hash('sha512', random_int(PHP_INT_MIN, PHP_INT_MAX));
		$this->_sessionsConfig->$sessionId = $session;

		$forceHTTPS = (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off');
		setcookie($this->_cookieName, $sessionId, $session['expireTime'], null, null, $forceHTTPS, true);
		// Force HTTPS if it's possible and enable HTTPOnly option.

		// User will be logged in next request!
	}

	public function logOut()
	{
		if (!$this->isUserLoggedIn()) {
			throw SessionManagerException::noUserLoggedIn();
		}

		$sessionId = $_COOKIE[$this->_cookieName];
		unset($this->_sessionsConfig->$sessionId);

		setcookie($this->_cookieName, null, 1);

		$this->_currentUserId = false;

		// Clean up expired sessions.
		foreach ($this->_sessionsConfig as $sessionId => $session) {
			if (time() > $session['expireTime']) {
				unset($this->_sessionsConfig->$sessionId);
			}
		}
	}

	public function closeOtherSessions()
	{
		if (!$this->isUserLoggedIn()) {
			throw SessionManagerException::noUserLoggedIn();
		}

		$successful = false;

		$currentSessionId = $_COOKIE[$this->_cookieName];

		foreach ($this->_sessionsConfig as $sessionId => $session) {
			if ($session['userId'] == $this->_currentUserId and $sessionId != $currentSessionId) {
				unset($this->_sessionsConfig->$sessionId);

				$successful = true;
			}
		}

		return $successful;
	}

	public function isUserLoggedIn()
	{
		return (bool)$this->_currentUserId;
	}

	public function getUserId()
	{
		return $this->_currentUserId ? $this->_currentUserId : false;
	}

	private function _generateWAI($userId) // WAI means "where am I?". This string is used to identify user agent.
	{
		return hash('sha512',
			$userId . $_SERVER['REMOTE_ADDR']
			. ((!empty($_SERVER['HTTP_USER_AGENT']))      ? $_SERVER['HTTP_USER_AGENT']      : '')
			. ((!empty($_SERVER['HTTP_ACCEPT']))          ? $_SERVER['HTTP_ACCEPT']          : '')
			. ((!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '')
			. ((!empty($_SERVER['HTTP_HOST']))            ? $_SERVER['HTTP_HOST']            : '')
		);
	}
}

class SessionManagerException extends __\Exception
{
	static public function noUserLoggedIn()
	{
		return new self('There is no active session, no user is logged in.', 1);
	}
	static public function alreadyUserLoggedIn($currentUserId)
	{
		return new self('User (with id ' . $currentUserId . ') is already logged in.', 2);
	}
}