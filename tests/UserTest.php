<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class UserTest extends TestCase
{
	public function testCheckPassword()
	{
		$examplePassword = uniqid();
		$exampleUser     = new __\User;

		$exampleUser->setPassword($examplePassword);

		$this->assertTrue($exampleUser->checkPassword($examplePassword));
	}
}