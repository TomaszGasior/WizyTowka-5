<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class SessionManagerTest extends TestCase
{
	private const INSTANCE_COOKIE_NAME = 'ExampleSessionInstance';
	private const INSTANCE_CONFIG_FILE = TEMP_FILES_DIR . '/SessionManager_sessions.conf';

	private const EXAMPLE_SESSION_DURATION = 3600;
	private const EXAMPLE_USER_ID          = 678;

	public function setUp() : void
	{
		__\ConfigurationFile::createNew(self::INSTANCE_CONFIG_FILE);
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);

		// $_SERVER elements undefined in CLI.
		$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0';

		// Start user session to prepare SessionManager's configuration file.
		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);
		$sessionManager->logIn(self::EXAMPLE_USER_ID, self::EXAMPLE_SESSION_DURATION);

		// Get session ID from newly created "Set-Cookie" HTTP header and simulate "next request" when cookie is present.
		$_COOKIE[self::INSTANCE_COOKIE_NAME] = $this->getLastHTTPCookie()['value'];
	}

	public function tearDown() : void
	{
		unlink(self::INSTANCE_CONFIG_FILE);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogIn() : void
	{
		// SessionManager::logIn() was invoked in setUp().

		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);

		// Check expire time of session cookie.
		$current  = $this->getLastHTTPCookie()['expires'];
		$expected = time() + self::EXAMPLE_SESSION_DURATION;
		$this->assertEquals($expected, $current);

		// Check cookie name.
		$current  = $this->getLastHTTPCookie()['name'];
		$expected = self::INSTANCE_COOKIE_NAME;
		$this->assertEquals($expected, $current);

		$sessionId = $this->getLastHTTPCookie()['value'];

		// Check session data in sessions configuration file.
		$current  = $sessionsConfigFile->$sessionId['userId'];
		$expected = self::EXAMPLE_USER_ID;
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testIsUserLoggedIn() : void
	{
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);
		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		// Check current user ID.
		$current  = $sessionManager->getUserId();
		$expected = self::EXAMPLE_USER_ID;
		$this->assertEquals($expected, $current);

		$this->assertTrue($sessionManager->isUserLoggedIn());
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogOut() : void
	{
		$sessionId = $this->getLastHTTPCookie()['value'];

		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);
		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$sessionManager->logOut();

		// Check whether session data was removed from sessions configuration file.
		$this->assertFalse(isset($sessionsConfigFile->$sessionId));

		// Check whether cookie is expired.
		$cookieExpireTime = $this->getLastHTTPCookie()['expires'];
		$this->assertTrue($cookieExpireTime < time());

		// Current data in SessionManager. User should be corretly logged out immediately.
		$this->assertNull($sessionManager->getUserId());
		$this->assertFalse($sessionManager->isUserLoggedIn());
	}

	/**
	* @runInSeparateProcess
	*/
	public function testExtraData() : void
	{
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);
		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$exampleName  = 'messages';
		$exampleValue = ['message first', 'message second', 'message third'];

		$sessionManager->setExtraData($exampleName, $exampleValue);

		$current  = $sessionManager->getExtraData($exampleName);
		$expected = $exampleValue;
		$this->assertEquals($expected, $current);

		$sessionId = $this->getLastHTTPCookie()['value'];

		// Check configuration file contents.
		$current  = $sessionsConfigFile->$sessionId['extraData'][$exampleName];
		$expected = $exampleValue;
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	* @expectedException     WizyTowka\_Private\SessionManagerException
	* @expectedExceptionCode 3
	*/
	public function testSetExtraDataInvalid() : void
	{
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);
		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$sessionManager->setExtraData('invalid', new \stdClass);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testCloseOtherSessions() : void
	{
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);
		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		// It's needed to prepare three simultaneous user sessions from different web browsers:
		//  * first for user with ID = EXAMPLE_USER_ID — already created inside setUp() method,
		//  * second for the same user from different browser,
		//  * third for another user.
		// When closeOtherSessions() is called, only second session should be destroyed.

		$userAgent_second = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36';
		$userAgent_third  = 'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko';

		// First it's needed to add second and third user sessions to configuration file and get session IDs.
		$_SERVER['HTTP_USER_AGENT'] = $userAgent_second;
		$tmpSessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);
		$tmpSessionManager->logIn(self::EXAMPLE_USER_ID, time() + 120);
		$sessionId_second = $this->getLastHTTPCookie()['value'];

		$_SERVER['HTTP_USER_AGENT'] = $userAgent_third;
		$tmpSessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);
		$tmpSessionManager->logIn(self::EXAMPLE_USER_ID + rand(10, 99), time() + 120);  // Different user ID.
		$sessionId_third = $this->getLastHTTPCookie()['value'];

		// Check configuration file contents before calling closeOtherSessions().
		$this->assertTrue(isset($sessionsConfigFile->$sessionId_second));
		$this->assertTrue(isset($sessionsConfigFile->$sessionId_third));

		// Run closeOtherSessions(): second session for current user should be closed.
		$sessionManager->closeOtherSessions();

		// Check configuration file contents after calling closeOtherSessions().
		$this->assertFalse(isset($sessionsConfigFile->$sessionId_second));
		$this->assertTrue(isset($sessionsConfigFile->$sessionId_third));

		// Create SessionManager instances for second and third user sessions.
		$_SERVER['HTTP_USER_AGENT'] = $userAgent_second;
		$_COOKIE[self::INSTANCE_COOKIE_NAME] = $sessionId_second;
		$sessionManager_second = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$_SERVER['HTTP_USER_AGENT'] = $userAgent_third;
		$_COOKIE[self::INSTANCE_COOKIE_NAME] = $sessionId_third;
		$sessionManager_third = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$this->assertFalse($sessionManager_second->isUserLoggedIn());
		$this->assertTrue($sessionManager_third->isUserLoggedIn());
	}
}