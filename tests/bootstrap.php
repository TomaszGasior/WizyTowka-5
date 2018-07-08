<?php

/**
* WizyTówka 5
* Bootstrap for unit tests.
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;


// Don't do it inside nested process (when @runInSeparateProcess annotation is used).
if ($_SERVER['SCRIPT_FILENAME'] ?? false) {
	// Prepare directory for temporary files.
	define('TEMP_FILES_DIR', __DIR__ . '/../TEMP_TESTS_' . time());
	mkdir(TEMP_FILES_DIR);

	// Copy "config.php" file to make defined paths relative to temporary directory.
	copy(__DIR__ . '/../code/config.php', TEMP_FILES_DIR . '/config.php');

	// Make symlink for "system" directory inside temporary directory.
	symlink(__DIR__ . '/../code/system', TEMP_FILES_DIR . '/system');

	// Clean up on shutdown.
	register_shutdown_function(function(){
		unlink(TEMP_FILES_DIR . "/config.php");
		unlink(TEMP_FILES_DIR . "/system");
		rmdir(TEMP_FILES_DIR);
	});
}

// Load config with system constants.
include TEMP_FILES_DIR . '/config.php';

// Init system without controller.
include __\SYSTEM_DIR . '/init.php';

// Leave error handling to PHPUnit.
restore_error_handler();
restore_exception_handler();


// Improved test case class used by all tests.
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
	// Assertion of HTML code. Needed in tests of HTML classes like HTMLMenu or HTMLHead.
	protected function assertHTMLEquals($expected, $current, ...$arguments) : void
	{
		$this->assertXmlStringEqualsXmlString(
			(@\DOMDocument::loadHTML($expected, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
			(@\DOMDocument::loadHTML($current,  LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
			...$arguments
		);
	}

	// This method gives ability to access private/protected methods.
	// In tests code instead of this syntax:       $object->_privateMethod('arg1', 'arg2');
	// use this:           $this->invokePrivateOn($object)->_privateMethod('arg1', 'arg2');
	protected function invokePrivateOn($object)
	{
		return new class($object)
		{
			static private $_object;

			public function __construct($object)
			{
				self::$_object = $object;
			}

			public function __get($property)
			{
				($reflection = new \ReflectionProperty(self::$_object, $property))->setAccessible(true);
				return $reflection->getValue();
			}

			public function __call($function, $arguments)
			{
				($reflection = new \ReflectionMethod(self::$_object, $function))->setAccessible(true);
				return $reflection->invokeArgs(self::$_object, $arguments);
			}
		};
	}

	// Get last HTTP header sent by header() function using Xdebug extension.
	// @runInSeparateProcess annotation is required.
	protected function getLastHTTPHeader() : string
	{
		if (!function_exists('xdebug_get_headers')) {
			throw new \Exception('Xdebug extension is required.');
		}

		return array_reverse(xdebug_get_headers())[0];
	}

	// Get properties of last HTTP cookie sent by setcookie() function.
	// @runInSeparateProcess annotation is required.
	protected function getLastHTTPCookie() : ?array
	{
		$found = preg_match(
			'/^Set-Cookie: (?<name>[^= ]+)=(?<value>[^ ;]+)(?:; expires=(?<expires>[^;]*))?/i',
			$this->getLastHTTPHeader(), $matches
		);

		if ($found) {
			return [
				'name'    => $matches['name'],
				'value'   => urldecode($matches['value']),
				'expires' => !empty($matches['expires']) ? strtotime($matches['expires']) : '',
			];
		}

		return null;
	}

	// Creates directory recursively with all parents.
	static protected function makeDirRecursive($directory) : void
	{
		mkdir($directory, 0777, true);
	}

	// Removes directory recursively with all contents.
	static protected function removeDirRecursive($directory) : void
	{
		$directoryContents = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($directoryContents as $file) {
			$file->isDir() ? rmdir($file) : unlink($file);
		}

		rmdir($directory);
	}
}