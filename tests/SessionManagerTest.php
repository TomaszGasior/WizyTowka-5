<?php

/**
* WizyTówka 5 — unit test
*/
class SessionManagerTest extends TestCase
{
	static private $_sessionsConfigFile = WizyTowka\CONFIG_DIR . '/sessions.conf';

	static private $_exampleUserId = 678;
	static private $_exampleSessionDuration = 3600;

	public function setUp()
	{
		// $_SERVER values undefined in CLI.
		$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0';

		// Prepare session configuration file for test only.
		@rename(self::$_sessionsConfigFile, self::$_sessionsConfigFile.'.bak');
		WizyTowka\ConfigurationFile::createNew(self::$_sessionsConfigFile);

		// Run user session manager.
		WizyTowka\SessionManager::setup();
	}

	public function tearDown()
	{
		@unlink(self::$_sessionsConfigFile);
		@rename(self::$_sessionsConfigFile.'.bak', self::$_sessionsConfigFile);

		// Workaround. SessionManager internally uses ConfigurationFile to get access to "sessions.conf".
		// ConfigurationFile in SessionManager contains old "sessions.conf" contents. It must be reloaded.
		if (file_exists(self::$_sessionsConfigFile)) {
			(new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile))->refresh();
		}
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogIn()
	{
		WizyTowka\SessionManager::logIn(self::$_exampleUserId, self::$_exampleSessionDuration);

		$cookieExpireTime   = $this->getLastHTTPCookie('expires');
		$sessionId          = $this->getLastHTTPCookie('value');
		$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);

		// Session data in sessions configuration file.
		$this->assertTrue(isset($sessionsConfigFile->$sessionId));

		// Session ID in session data from configuration file.
		$current  = $sessionsConfigFile->$sessionId['userId'];
		$expected = self::$_exampleUserId;
		$this->assertEquals($expected, $current);

		// Expire time of session cookie.
		$current  = $cookieExpireTime;
		$expected = time() + self::$_exampleSessionDuration;
		$this->assertEquals($expected, $current);

		// Current data in SessionManager trait.
		$this->assertTrue(
			WizyTowka\SessionManager::isUserLoggedIn()
		);
		$current  = WizyTowka\SessionManager::getUserId();
		$expected = self::$_exampleUserId;
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogOut()
	{
		// Log in second time. Cookie from previous test wasn't kept because of @runInSeparateProcess.
		WizyTowka\SessionManager::logIn(self::$_exampleUserId, self::$_exampleSessionDuration);

		$sessionId = $this->getLastHTTPCookie('value');

		WizyTowka\SessionManager::logOut();

		$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);
		$cookieExpireTime   = $this->getLastHTTPCookie('expires');

		// Session data in sessions configuration file.
		$this->assertFalse(isset($sessionsConfigFile->$sessionId));

		// Expire time of session cookie.
		$this->assertTrue($cookieExpireTime < time());

		// Current data in SessionManager trait.
		$this->assertFalse(WizyTowka\SessionManager::isUserLoggedIn());
		$this->assertFalse(WizyTowka\SessionManager::getUserId());
	}
}