<?php

/**
* WizyTówka 5
* Uploaded file class. Manages files sent to website as attachments or media.
*/
namespace WizyTowka;

class UploadedFile
{
	private $_filename;

	private function __construct() {}

	private function __clone() {}

	public function __debugInfo()
	{
		return [
			'name' => $this->_filename,
		];
	}

	public function getName()
	{
		return $this->_filename;
	}

	public function getPath()
	{
		return FILES_DIR . '/' . $this->_filename;
	}

	public function getURL()
	{
		return FILES_URL . '/' . $this->_filename;
	}

	public function getSize()
	{
		return is_file($this->getPath()) ? (int)filesize($this->getPath()) : 0;
	}

	public function rename($newFilename)
	{
		// Avoid creating subdirectories.
		if (strpos($newFilename, '/') !== false and strpos($newFilename, '\\') !== false) {
			return false;
		}

		$newFilenamePath = FILES_DIR . '/' . $newFilename;

		if (!file_exists($newFilenamePath) and is_file($this->getPath()) and rename($this->getPath(), $newFilenamePath)) {
			$this->_filename = $newFilename;
			return true;
		}

		return false;
	}

	public function delete()
	{
		return (is_file($this->getPath()) and unlink($this->getPath()));
	}

	static public function getByName($filename)
	{
		if (is_file(FILES_DIR . '/' . $filename)) {
			$fileObject = new static;
			$fileObject->_filename = $filename;

			return $fileObject;
		}

		return false;
	}

	static public function getAll()
	{
		$uploadedFiles = glob(FILES_DIR . '/*');
		if ($uploadedFiles === false) {
			// Notice: if directory is empty, glob() should return empty array,
			// but it is possible to return false on some operating systems.
			// More here: http://php.net/manual/en/function.glob.php#refsect1-function.glob-returnvalues
			return [];
		}

		$elementsToReturn = [];
		foreach ($uploadedFiles as $filepath) {
			$elementsToReturn[] = static::getByName(basename($filepath));
		}

		return array_filter($elementsToReturn);  // Skip "false" boolean values returned by getByName().
	}
}