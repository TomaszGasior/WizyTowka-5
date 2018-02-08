<?php

/**
* WizyTówka 5 — unit test
*/
class SessionManagerTest extends TestCase
{
	static private $_sessionsConfigFile = WizyTowka\CONFIG_DIR . '/sessions.conf';

	static private $_exampleUserId = 678;
	static private $_exampleSessionDuration = 3600;

	static public function setUpBeforeClass()
	{
		// Workaround for PHPUnit because of @runInSeparateProcess. Run this method only once before first test.
		if (!headers_sent()) { return; }

		// $_SERVER values undefined in CLI.
		$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0';

		// Prepare session configuration file for test only.
		@rename(self::$_sessionsConfigFile, self::$_sessionsConfigFile.'.bak');
		WizyTowka\ConfigurationFile::createNew(self::$_sessionsConfigFile);
	}

	static public function tearDownAfterClass()
	{
		// Workaround for PHPUnit because of @runInSeparateProcess. Run this method only once after all tests.
		if (!headers_sent()) { return; }

		@unlink(self::$_sessionsConfigFile);
		@rename(self::$_sessionsConfigFile.'.bak', self::$_sessionsConfigFile);

		@unlink('keptHTTPCookie');
	}

	public function setUp()
	{
		// Restore kept HTTP cookie to $_COOKIE superglobal.
		if ($keptHTTPCookie = $this->_getKeptHTTPCookie()) {
			$_COOKIE[$keptHTTPCookie['name']] = $keptHTTPCookie['value'];
		}

		WizyTowka\SessionManager::setup();
	}

	private function _keepHTTPCookie()
	{
		file_put_contents('keptHTTPCookie', serialize($this->getLastHTTPCookie()));
	}

	private function _getKeptHTTPCookie()
	{
		return file_exists('keptHTTPCookie') ? unserialize(file_get_contents('keptHTTPCookie')) : false;
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogIn()
	{
		WizyTowka\SessionManager::logIn(self::$_exampleUserId, self::$_exampleSessionDuration);

		$sessionId          = $this->getLastHTTPCookie()['value'];
		$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);

		// Check session data in sessions configuration file.
		$current  = $sessionsConfigFile->$sessionId['userId'];
		$expected = self::$_exampleUserId;
		$this->assertEquals($expected, $current);

		// Check expire time of session cookie.
		$current  = $this->getLastHTTPCookie()['expires'];
		$expected = time() + self::$_exampleSessionDuration;
		$this->assertEquals($expected, $current);

		// Save HTTP cookie for next tests. User should be corretly logged in during next HTTP request.
		$this->_keepHTTPCookie();
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogIn_InNextRequest()
	{
		// Current data in SessionManager trait.
		$current  = WizyTowka\SessionManager::getUserId();
		$expected = self::$_exampleUserId;
		$this->assertEquals($expected, $current);

		$this->assertTrue(WizyTowka\SessionManager::isUserLoggedIn());
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogOut()
	{
		WizyTowka\SessionManager::logOut();

		$sessionId          = $this->_getKeptHTTPCookie()['value'];
		$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);

		// Check whether session data was removed from sessions configuration file.
		$this->assertFalse(isset($sessionsConfigFile->$sessionId));

		// Check whether session ID was removed from cookie.
		$current     = $this->getLastHTTPCookie()['value'];
		$notExpected = $sessionId;
		$this->assertNotEquals($notExpected, $current);

		// Check whether cookie is expired.
		$cookieExpireTime = $this->getLastHTTPCookie()['expires'];
		$this->assertTrue($cookieExpireTime < time());

		// Current data in SessionManager trait. User should be corretly logged out immediately.
		$this->assertFalse(WizyTowka\SessionManager::getUserId());
		$this->assertFalse(WizyTowka\SessionManager::isUserLoggedIn());
	}
}