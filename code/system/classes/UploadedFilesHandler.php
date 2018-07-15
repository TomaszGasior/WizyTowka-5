<?php

/**
* WizyTówka 5
* Uploaded files handler. It loops through $_FILES['field'] and moves newly sent files to proper directory.
*/
namespace WizyTowka;

class UploadedFilesHandler
{
	// These constants should have values bigger than PHP's UPLOAD_ERR_* constants.
	// More here: http://php.net/manual/en/features.file-upload.errors.php
	const ERROR_MOVE_UPLOADED_FILE = 0b0001000000000;
	const ERROR_FILE_STILL_EXISTS  = 0b0010000000000;
	const ERROR_FILE_TOO_BIG       = 000100000000000;

	// This array should contain file names as keys and error codes as values.
	// Sending was successful if this array is empty.
	private $_errors = [];

	// Amount of sent (successfully or not) files.
	private $_filesCount = 0;

	// Settings.
	private $_maxFileSizeBytes   = 0;
	private $_lowercaseFileNames = true;

	private $_handled = false;

	public function __construct(int $maxFileSizeBytes = 0, bool $lowercaseFileNames = true)
	{
		$this->_maxFileSizeBytes   = $maxFileSizeBytes >= 0 ? $maxFileSizeBytes : 0;
		$this->_lowercaseFileNames = $lowercaseFileNames;
	}

	public function handleSentFiles(array $_FILESField) : void
	{
		if ($this->_handled) {
			throw UploadedFilesHandlerException::alreadyHandled(__FUNCTION__);
		}
		$this->_handled = true;

		// Stop when structure of $_FILES item is wrong.
		foreach (['name', 'tmp_name', 'size', 'error'] as $key) {
			if (!is_array($_FILESField[$key]) or count($_FILESField[$key]) != count($_FILESField['name'])) {
				throw UploadedFilesHandlerException::wrongFILESVariable();
			}
		}

		foreach ($_FILESField['name'] as $key => $fileName) {
			$tempFilePath  = $_FILESField['tmp_name'][$key];
			$errorCode     = $_FILESField['error']   [$key];
			$fileSizeBytes = $_FILESField['size']    [$key];

			$safeFileName = (new Text($fileName))->makeSlug($this->_lowercaseFileNames)->get();

			// Skip if PHP encountered error.
			if ($errorCode != \UPLOAD_ERR_OK) {
				$this->_errors[$safeFileName] = $errorCode;
				continue;
			}

			// Skip if file is too big.
			if ($fileSizeBytes > $this->_maxFileSizeBytes) {
				$this->_errors[$safeFileName] = self::ERROR_FILE_TOO_BIG;
				continue;
			}

			// Avoid overwriting existing files. Try to append unix timestamp to file name.
			if (UploadedFile::getByName($safeFileName)) {
				// "example.file.name.png" --> "example.file.name_1517779327.png"
				$safeFileNameParts = array_reverse(explode('.', $safeFileName));
				$safeFileNameParts[1] .= '_' . time();
				$newSafeFileName = implode('.', array_reverse($safeFileNameParts));

				// File still exists!? Skip it.
				if (UploadedFile::getByName($newSafeFileName)) {
					$this->_errors[$safeFileName] = self::ERROR_FILE_STILL_EXISTS;
					continue;
				}

				$safeFileName = $newSafeFileName;
			}

			// Try to move uploaded file to proper place.
			try {
				$desitationFilePath     = FILES_DIR . '/' . $safeFileName;
				$moveUploadedFileResult = move_uploaded_file($tempFilePath, $desitationFilePath);
			} catch (\ErrorException $e) {
				$moveUploadedFileResult = false;
				WT()->errors->addToLog($e);
			}

			// Skip if move_uploaded_file() failed.
			if (!$moveUploadedFileResult) {
				$this->_errors[$safeFileName] = self::ERROR_MOVE_UPLOADED_FILE;
			}
		}
	}

	public function countMoved() : ?int
	{
		return $this->_handled ? ($this->_filesCount - count($this->_errors)) : null;
	}

	public function countErrors() : ?int
	{
		return $this->_handled ? count($this->_errors) : null;
	}

	public function getErrors() : ?array
	{
		return $this->_handled ? $this->_errors : null;
	}

	public function getMaxFileSize() : ?int
	{
		// This function parses PHP's shorthand bytes syntax.
		// More here: http://php.net/manual/en/faq.using.php#faq.using.shorthandbytes
		$parseIniOption = function($name)
		{
			$factors = [
				'K' => 1024,
				'M' => 1048576,
				'G' => 1073741824,
			];
			$value   = strtoupper((string)ini_get($name));
			$number  = preg_replace('/[^\-0-9]/', null, $value);
			$unit    = preg_replace('/[^KMG]/', null, $value);

			if (is_numeric($number) and isset($factors[$unit])) {
				$number *= $factors[$unit];
			}

			return $number;
		};

		$possibleValues = array_filter([
			$parseIniOption('post_max_size'),
			$parseIniOption('upload_max_filesize'),
			$this->_maxFileSizeBytes
		], function($value){ return $value > 0; });

		return $possibleValues ? (int)min($possibleValues) : null;
	}

	public function getMaxFilesNumber() : ?int
	{
		return (ini_get('max_file_uploads') > 0) ? (int)ini_get('max_file_uploads') : null;
	}

	public function isSendingFilesEnabled() : bool
	{
		return (ini_get('file_uploads') and ini_get('max_file_uploads') > 0);
	}
}

class UploadedFilesHandlerException extends Exception
{
	static public function wrongFILESVariable()
	{
		return new self('Structure of given $_FILES array item is wrong.', 1);
	}

	static public function alreadyHandled($method)
	{
		return new self($method . '() cannot be invoked more than once.', 2);
	}
}