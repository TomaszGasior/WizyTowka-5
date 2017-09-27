<?php

/**
* WizyTówka 5 — unit test
*/
class UserTest extends PHPUnit\Framework\TestCase
{
	public function testCheckPassword()
	{
		$examplePassword = uniqid();
		$exampleUser     = new WizyTowka\User;

		$exampleUser->setPassword($examplePassword);

		$this->assertTrue($exampleUser->checkPassword($examplePassword));
	}
}