<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class SessionManagerTest extends TestCase
{
	static private $_sessionsCookieName = 'ExampleSessionInstance';
	static private $_sessionsConfigFilePath = __\CONFIG_DIR . '/sessions_test.conf';

	static private $_sessionsConfigFileContent = <<< 'CODE_JSON'
{
    "%s": {
        "userId": 678,
        "waiString": "1c4a5c73a08b4311959b415124e25fe3859249ef5a7ff5c946fb0bdca614832b0469f9315d1744fc47af96199c9390e34da48bdfcd75e0eab2363c45b7ac67f6",
        "expireTime": %d,
        "reloginTime": %d
    }
}
CODE_JSON;

	static private $_exampleUserId = 678;
	static private $_exampleSessionId = '52e91938219800668c14859e756cfc87c61c92f2aeeafe412444455af9c4e3a9b8d2cfcac84f86ad2f55e88c266a67f7db86e1112e2f46ee13fe968b3ec8acb6';
	static private $_exampleSessionDuration = 3600;

	public function setUp()
	{
		// $_SERVER values undefined in CLI.
		$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0';
	}

	public function tearDown()
	{
		@unlink(self::$_sessionsConfigFilePath);
	}

	private function _prepareFakeSession()
	{
		file_put_contents(
			self::$_sessionsConfigFilePath,
			sprintf(
				self::$_sessionsConfigFileContent,
				self::$_exampleSessionId, time() + self::$_exampleSessionDuration, time() + 120
			)
		);

		$_COOKIE[self::$_sessionsCookieName] = self::$_exampleSessionId;
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogIn()
	{
		__\ConfigurationFile::createNew(self::$_sessionsConfigFilePath);
		$sessionsConfigFile = new __\ConfigurationFile(self::$_sessionsConfigFilePath);

		$sessionManager = new __\_Private\SessionManager(self::$_sessionsCookieName, $sessionsConfigFile);

		$sessionManager->logIn(self::$_exampleUserId, self::$_exampleSessionDuration);

		// Check expire time of session cookie.
		$current  = $this->getLastHTTPCookie()['expires'];
		$expected = time() + self::$_exampleSessionDuration;
		$this->assertEquals($expected, $current);

		// Check cookie name.
		$current  = $this->getLastHTTPCookie()['name'];
		$expected = self::$_sessionsCookieName;
		$this->assertEquals($expected, $current);

		$sessionId = $this->getLastHTTPCookie()['value'];

		// Check session data in sessions configuration file.
		$current  = $sessionsConfigFile->$sessionId['userId'];
		$expected = self::$_exampleUserId;
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testIsUserLoggedIn()
	{
		$this->_prepareFakeSession();
		$sessionsConfigFile = new __\ConfigurationFile(self::$_sessionsConfigFilePath);

		$sessionManager = new __\_Private\SessionManager(self::$_sessionsCookieName, $sessionsConfigFile);

		// Check current user ID.
		$current  = $sessionManager->getUserId();
		$expected = self::$_exampleUserId;
		$this->assertEquals($expected, $current);

		$this->assertTrue($sessionManager->isUserLoggedIn());
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogOut()
	{
		$this->_prepareFakeSession();
		$sessionsConfigFile = new __\ConfigurationFile(self::$_sessionsConfigFilePath);

		$sessionManager = new __\_Private\SessionManager(self::$_sessionsCookieName, $sessionsConfigFile);

		$sessionManager->logOut();

		// Check whether session data was removed from sessions configuration file.
		$this->assertFalse(isset($sessionsConfigFile->{self::$_exampleSessionId}));

		// Check whether session ID was removed from cookie.
		$current     = $this->getLastHTTPCookie()['value'];
		$notExpected = self::$_exampleSessionId;
		$this->assertNotEquals($notExpected, $current);

		// Check whether cookie is expired.
		$cookieExpireTime = $this->getLastHTTPCookie()['expires'];
		$this->assertTrue($cookieExpireTime < time());

		// Current data in SessionManager. User should be corretly logged out immediately.
		$this->assertNull($sessionManager->getUserId());
		$this->assertFalse($sessionManager->isUserLoggedIn());
	}
}