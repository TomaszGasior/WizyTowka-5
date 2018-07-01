<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class UploadedFileTest extends TestCase
{
	static private $_uploadsDir = __\FILES_DIR;

	static public function setUpBeforeClass()
	{
		@rename(self::$_uploadsDir, self::$_uploadsDir.'.bak');
		@mkdir(self::$_uploadsDir);
		@mkdir(self::$_uploadsDir . '/subdir');  // Files placed here won't be discoverable for class.

		// Prepare example files.
		$files = [
			'file_2048_bytes'  => str_repeat('a', 2048),
			'file_to_change'   => 'Contents of this file will be changed to test modification time.',
			'file_to_delete'   => 'This file will be moved to the end of earth.',
			'file_to_rename'   => 'Name of this file will be changed.',
		];
		foreach ($files as $file => $contents) {
			file_put_contents(self::$_uploadsDir . '/' . $file, $contents);
		}
		foreach ($files as $file => $contents) {
			file_put_contents(self::$_uploadsDir . '/subdir/' . $file, $contents);
		}
	}

	static public function tearDownAfterClass()
	{
		foreach (glob(self::$_uploadsDir . '/subdir/*') as $file) {
			@unlink($file);
		}
		@rmdir(self::$_uploadsDir . '/subdir');

		foreach (glob(self::$_uploadsDir . '/*') as $file) {
			@unlink($file);
		}
		@rmdir(self::$_uploadsDir);

		@rename(self::$_uploadsDir.'.bak', self::$_uploadsDir);
	}

	public function testGetAll()
	{
		$current  = __\UploadedFile::getAll();
		$expected = [
			__\UploadedFile::getByName('file_2048_bytes'),
			__\UploadedFile::getByName('file_to_change'),
			__\UploadedFile::getByName('file_to_delete'),
			__\UploadedFile::getByName('file_to_rename'),
		];
		$this->assertEquals($expected, $current);
	}

	public function testGetPath()
	{
		$file = __\UploadedFile::getByName('file_2048_bytes');

		$current  = $file->getPath();
		$expected = self::$_uploadsDir . '/file_2048_bytes';
		$this->assertEquals($expected, $current);
	}

	public function testGetURL()
	{
		$file = __\UploadedFile::getByName('file_2048_bytes');

		$current  = $file->getURL();
		$expected = __\FILES_URL . '/file_2048_bytes';
		$this->assertEquals($expected, $current);
	}

	public function testGetSize()
	{
		$file = __\UploadedFile::getByName('file_2048_bytes');

		$current  = $file->getSize();
		$expected = 2048;
		$this->assertEquals($expected, $current);
	}

	public function testGetModificationTime()
	{
		$file = __\UploadedFile::getByName('file_to_change');

		file_put_contents($file->getPath(), rand(9, 99999));

		$current  = $file->getModificationTime();
		$expected = time();
		$this->assertEquals($expected, $current);
	}

	public function testRename()
	{
		$file = __\UploadedFile::getByName('file_to_rename');

		$this->assertTrue($file->rename('successfully_renamed_file'));

		$this->assertFileNotExists(self::$_uploadsDir . '/file_to_rename');
		$this->assertFileExists(self::$_uploadsDir . '/successfully_renamed_file');
	}

	public function testRenameNotOverwrite()
	{
		$file = __\UploadedFile::getByName('successfully_renamed_file');

		$this->assertFalse($file->rename('file_2048_bytes'));

		$this->assertFileExists(self::$_uploadsDir . '/file_2048_bytes');
		$this->assertFileExists(self::$_uploadsDir . '/successfully_renamed_file');
	}

	public function testDelete()
	{
		$file = __\UploadedFile::getByName('file_to_delete');

		$this->assertTrue($file->delete());

		$this->assertFileNotExists(self::$_uploadsDir . '/file_to_delete');
	}
}