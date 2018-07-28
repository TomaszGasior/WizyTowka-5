<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class UploadedFilesHandlerTest extends TestCase
{
	private const UPLOADS_DIR = __\FILES_DIR;

	public function setUp() : void
	{
		self::makeDirRecursive(self::UPLOADS_DIR);

		$_FILES->overwrite('sent_files', [
			'name' => [
				0 => 'appearance__file-chooser.patch',
				1 => 'Exception.php',
				2 => 'HTMLFormFields.md',
				3 => 'Official Video Clip_EDIT.mp4',
				4 => 'Top secret.docx',
			],
			'type' => [
				0 => 'text/x-patch',
				1 => 'application/x-php',
				2 => 'text/markdown',
				3 => 'video/mp4',
				4 => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			],
			'tmp_name' => [
				0 => '/tmp/php7FOq1x',
				1 => '/tmp/phpRgQNrw',
				2 => '/tmp/phpvUtbSu',
				3 => '/tmp/phpzLHzit',
				4 => '/tmp/phpJhjQJr',
			],
			'error' =>[
				0 => \UPLOAD_ERR_OK,
				1 => \UPLOAD_ERR_OK,
				2 => \UPLOAD_ERR_EXTENSION,
				3 => \UPLOAD_ERR_OK,
				4 => \UPLOAD_ERR_PARTIAL,
			],
			'size' => [
				0 => '1593',
				1 => '2088',
				2 => '7775',
				3 => '2162324',
				4 => '31022',
			],
		]);
	}

	public function tearDown() : void
	{
		self::removeDirRecursive(__\DATA_DIR);
	}

	public function testHandleSentFiles() : void
	{
		$handler = new __\UploadedFilesHandler(31022, false);

		$handler->handleSentFiles($_FILES['sent_files']);

		$current  = $handler->getErrors();
		$expected = [
			'appearance__file-chooser.patch' => __\UploadedFilesHandler::ERROR_MOVE_UPLOADED_FILE,
			'Exception.php'                  => __\UploadedFilesHandler::ERROR_MOVE_UPLOADED_FILE,
			'HTMLFormFields.md'              => \UPLOAD_ERR_EXTENSION,
			'Official-Video-Clip_EDIT.mp4'   => __\UploadedFilesHandler::ERROR_FILE_TOO_BIG,
			'Top-secret.docx'                => \UPLOAD_ERR_PARTIAL,
			// ERROR_MOVE_UPLOADED_FILE is returned because move_uploaded_file() fails with fake data
			// used in test environment. It works poperly only when real files are sent by HTTP request.
		];
		$this->assertEquals($expected, $current);
	}

	/**
	* @expectedException     WizyTowka\UploadedFilesHandlerException
	* @expectedExceptionCode 1
	*/
	public function testHandleSentFilesInvaildData() : void
	{
		$handler = new __\UploadedFilesHandler(31022, false);

		$array = $_FILES['sent_files'];
		unset($array['tmp_name']);
		$_FILES->overwrite('sent_files', $array);

		$handler->handleSentFiles($_FILES['sent_files']);
	}

	/**
	* @expectedException     WizyTowka\UploadedFilesHandlerException
	* @expectedExceptionCode 2
	*/
	public function testHandleSentFilesInvokedTwice() : void
	{
		$handler = new __\UploadedFilesHandler(31022, false);

		$handler->handleSentFiles($_FILES['sent_files']);
		$handler->handleSentFiles($_FILES['sent_files']);
	}

	public function testGetMaxFileSize() : void
	{
		$customMaxSize = 201; // Bytes.

		$handler = new __\UploadedFilesHandler($customMaxSize);

		$current  = $handler->getMaxFileSize();
		$expected = $customMaxSize;
		$this->assertLessThanOrEqual($expected, $current);
	}

	public function testGetMaxFilesNumber() : void
	{
		$handler = new __\UploadedFilesHandler;

		$current  = $handler->getMaxFilesNumber();
		$expected = ini_get('max_file_uploads');
		$this->assertEquals($expected, $current);
	}

	public function testIsSendingFilesEnabled() : void
	{
		$handler = new __\UploadedFilesHandler;

		$current  = $handler->isSendingFilesEnabled();
		$expected = (ini_get('file_uploads') and ini_get('max_file_uploads') > 0);
		$this->assertEquals($expected, $current);
	}
}