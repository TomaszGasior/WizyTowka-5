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

	private $_currentSessionId = null;
	private $_newSessionIdNextTime = null;

	public function __construct(string $cookieName, __\ConfigurationFile $config)
	{
		$this->_cookieName     = $cookieName;
		$this->_sessionsConfig = $config;

		$sessionId = $_COOKIE[$this->_cookieName] ?? false;
		if ($sessionId) {
			$session = $this->_sessionsConfig->$sessionId ?? false;

			if ($session and $session['waiString'] == $this->_generateWAI($session['userId']) and time() < $session['expireTime']) {
				$this->_currentSessionId = $sessionId;

				// Periodically re-login to change session ID for better security.
				if (time() > $session['reloginTime']) {
					$this->logOut();
					$this->logIn($session['userId'], $session['expireTime'] - time());
					$this->_currentSessionId = $this->_newSessionIdNextTime;
				}
			}
			// If session is expired or WAI string is incorrect, destroy session data such as when user is logged out.
			else {
				$this->_currentSessionId = -1;  // Fake value needed by logOut().
				$this->logOut();
			}
		}
	}

	public function isUserLoggedIn() : bool
	{
		return (bool)$this->_currentSessionId;
	}

	public function getUserId() : ?int
	{
		return $this->_sessionsConfig->{$this->_currentSessionId}['userId'] ?? null;
	}

	public function logIn(int $userId, string $sessionDuration) : void
	{
		if ($this->isUserLoggedIn()) {
			throw SessionManagerException::alreadyUserLoggedIn($this->_currentUserId);
		}

		$session = [
			'userId'      => $userId,
			'waiString'   => $this->_generateWAI($userId),
			'expireTime'  => time() + (integer)$sessionDuration,
			'reloginTime' => time() + 120,
			'extraData'   => [],
		];

		$sessionId = hash('sha512', random_int(PHP_INT_MIN, PHP_INT_MAX));
		$this->_sessionsConfig->$sessionId = $session;

		$forceHTTPS = (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off');
		setcookie($this->_cookieName, $sessionId, $session['expireTime'], null, null, $forceHTTPS, true);
		// Force HTTPS if it's possible and enable HTTPOnly option.

		$this->_newSessionIdNextTime = $sessionId;  // Needed for re-login function.

		// User will be logged in next request!
	}

	public function logOut() : void
	{
		if (!$this->isUserLoggedIn()) {
			throw SessionManagerException::noUserLoggedIn();
		}

		setcookie($this->_cookieName, null, 1);

		unset($this->_sessionsConfig->{$this->_currentSessionId});
		$this->_currentSessionId = null;

		// Clean up expired sessions.
		foreach ($this->_sessionsConfig as $sessionId => $session) {
			if (time() > $session['expireTime']) {
				unset($this->_sessionsConfig->$sessionId);
			}
		}
	}

	public function closeOtherSessions() : bool
	{
		if (!$this->isUserLoggedIn()) {
			throw SessionManagerException::noUserLoggedIn();
		}

		$successful = false;

		foreach ($this->_sessionsConfig as $sessionId => $session) {
			if ($session['userId'] == $this->getUserId() and $sessionId != $this->_currentSessionId) {
				unset($this->_sessionsConfig->$sessionId);

				$successful = true;
			}
		}

		return $successful;
	}

	public function getExtraData(string $name)
	{
		if (!$this->isUserLoggedIn()) {
			throw SessionManagerException::noUserLoggedIn();
		}

		$session = $this->_sessionsConfig->{$this->_currentSessionId};

		return $session['extraData'][$name] ?? null;
	}

	public function setExtraData(string $name, $value) : void
	{
		if (!$this->isUserLoggedIn()) {
			throw SessionManagerException::noUserLoggedIn();
		}

		$session = $this->_sessionsConfig->{$this->_currentSessionId};

		if ($value === null) {
			unset($session['extraData'][$name]);
		}
		else {
			if (!is_scalar($value) and !is_array($value)) {
				throw SessionManagerException::invalidExtraDataValue($name);
			}

			$session['extraData'][$name] = $value;
		}

		$this->_sessionsConfig->{$this->_currentSessionId} = $session;
	}

	private function _generateWAI(int $userId) : string // WAI means "where am I?". This string is used to identify user agent.
	{
		return hash('sha512',
			$userId . $_SERVER['REMOTE_ADDR']
			. ($_SERVER['HTTP_USER_AGENT']      ?? '') . ($_SERVER['HTTP_ACCEPT'] ?? '')
			. ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') . ($_SERVER['HTTP_HOST']   ?? '')
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
	static public function invalidExtraDataValue($name)
	{
		return new self('Extra data "' . $name . '" must be a scalar value or array.', 3);
	}
}