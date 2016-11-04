<?php

/**
* WizyTówka 5 — unit test
*/
class WTExceptionTest extends PHPUnit\Framework\TestCase
{
	public function testDuplicatedExceptionsCodes()
	{
		$scriptOutput = shell_exec('php ' . __DIR__ . '/../others/ExceptionsList.php --exceptions-duplicates');
		$exceptionsCodesDuplicates = json_decode($scriptOutput);

		if ($exceptionsCodesDuplicates !== null) {  // Check whether ExceptionList.php script exists.
			$this->assertEmpty($exceptionsCodesDuplicates, 'Code of exceptions is duplicated.');
		}
	}
}
