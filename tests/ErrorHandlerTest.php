<?php

class ErrorHandlerTest extends PHPUnit\Framework\TestCase
{
	static private $errorLogPath = 'code/data/config/errors.log';

	static public function setUpBeforeClass()
	{
		if (file_exists(self::$errorLogPath)) {
			rename(self::$errorLogPath, self::$errorLogPath.'.bak');
		}
	}

	/**
	 * @expectedException        ErrorException
	 * @expectedExceptionMessage Example error
	 */
	public function testConvertErrorToException()
	{
		WizyTowka\ErrorHandler::convertErrorToException(E_WARNING, 'Example error', 'examplefile.txt', 1);
	}

	public function testErrorHandler()
	{
		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		WizyTowka\ErrorHandler::handleException(new Exception($exceptionMessage, 8));

		$errorLog = file_get_contents(self::$errorLogPath);
		$this->assertContains($exceptionMessage, $errorLog);
	}

	static public function tearDownAfterClass()
	{
		if (file_exists(self::$errorLogPath)) {
			unlink(self::$errorLogPath);
		}
		if (file_exists(self::$errorLogPath.'.bak')) {
			rename(self::$errorLogPath.'.bak', self::$errorLogPath);
		}
	}
}