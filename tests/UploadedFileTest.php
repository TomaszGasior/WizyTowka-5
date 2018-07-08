<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class UploadedFileTest extends TestCase
{
	private const UPLOADS_DIR = __\FILES_DIR;

	static public function setUpBeforeClass() : void
	{
		self::makeDirRecursive(self::UPLOADS_DIR);
		self::makeDirRecursive(self::UPLOADS_DIR . '/subdir');  // Files placed here won't be discoverable for class.

		// Prepare example files.
		$files = [
			'file_2048_bytes'  => str_repeat('a', 2048),
			'file_to_change'   => 'Contents of this file will be changed to test modification time.',
			'file_to_delete'   => 'This file will be moved to the end of earth.',
			'file_to_rename'   => 'Name of this file will be changed.',
		];
		foreach ($files as $file => $contents) {
			file_put_contents(self::UPLOADS_DIR . '/' . $file, $contents);
		}
		foreach ($files as $file => $contents) {
			file_put_contents(self::UPLOADS_DIR . '/subdir/' . $file, $contents);
		}
	}

	static public function tearDownAfterClass() : void
	{
		self::removeDirRecursive(__\DATA_DIR);
	}

	public function testGetAll() : void
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

	public function testGetPath() : void
	{
		$file = __\UploadedFile::getByName('file_2048_bytes');

		$current  = $file->getPath();
		$expected = self::UPLOADS_DIR . '/file_2048_bytes';
		$this->assertEquals($expected, $current);
	}

	public function testGetURL() : void
	{
		$file = __\UploadedFile::getByName('file_2048_bytes');

		$current  = $file->getURL();
		$expected = __\FILES_URL . '/file_2048_bytes';
		$this->assertEquals($expected, $current);
	}

	public function testGetSize() : void
	{
		$file = __\UploadedFile::getByName('file_2048_bytes');

		$current  = $file->getSize();
		$expected = 2048;
		$this->assertEquals($expected, $current);
	}

	public function testGetModificationTime() : void
	{
		$file = __\UploadedFile::getByName('file_to_change');

		file_put_contents($file->getPath(), rand(9, 99999));

		$current  = $file->getModificationTime();
		$expected = time();
		$this->assertEquals($expected, $current);
	}

	public function testRename() : void
	{
		$file = __\UploadedFile::getByName('file_to_rename');

		$this->assertTrue($file->rename('successfully_renamed_file'));

		$this->assertFileNotExists(self::UPLOADS_DIR . '/file_to_rename');
		$this->assertFileExists(self::UPLOADS_DIR . '/successfully_renamed_file');
	}

	public function testRenameNotOverwrite() : void
	{
		$file = __\UploadedFile::getByName('successfully_renamed_file');

		$this->assertFalse($file->rename('file_2048_bytes'));

		$this->assertFileExists(self::UPLOADS_DIR . '/file_2048_bytes');
		$this->assertFileExists(self::UPLOADS_DIR . '/successfully_renamed_file');
	}

	public function testDelete() : void
	{
		$file = __\UploadedFile::getByName('file_to_delete');

		$this->assertTrue($file->delete());

		$this->assertFileNotExists(self::UPLOADS_DIR . '/file_to_delete');
	}
}