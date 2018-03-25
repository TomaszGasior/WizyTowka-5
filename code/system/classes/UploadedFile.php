<?php

/**
* WizyTówka 5
* Uploaded file class. Manages files sent to website as attachments or media.
*/
namespace WizyTowka;

class UploadedFile
{
	private $_fileName;

	private function __construct() {}

	private function __clone() {}

	public function __debugInfo()
	{
		return [
			'name' => $this->_fileName,
		];
	}

	public function getName()
	{
		return $this->_fileName;
	}

	public function getPath()
	{
		return FILES_DIR . '/' . $this->_fileName;
	}

	public function getURL()
	{
		return FILES_URL . '/' . $this->_fileName;
	}

	public function getSize()
	{
		return is_file($this->getPath()) ? (int)filesize($this->getPath()) : 0;
	}

	public function getModificationTime()
	{
		return is_file($this->getPath()) ? (int)filemtime($this->getPath()) : 0;
	}

	public function rename($newFileName)
	{
		// Avoid creating subdirectories.
		if (strpos($newFileName, '/') !== false or strpos($newFileName, '\\') !== false) {
			return false;
		}

		$newFileNamePath = FILES_DIR . '/' . $newFileName;

		if (!file_exists($newFileNamePath) and is_file($this->getPath()) and rename($this->getPath(), $newFileNamePath)) {
			$this->_fileName = $newFileName;
			return true;
		}

		return false;
	}

	public function delete()
	{
		return (is_file($this->getPath()) and unlink($this->getPath()));
	}

	static public function getByName($fileName)
	{
		// Avoid reading from subdirectories.
		if (strpos($fileName, '/') !== false or strpos($fileName, '\\') !== false) {
			return false;
		}

		if (is_file(FILES_DIR . '/' . $fileName)) {
			$fileObject = new static;
			$fileObject->_fileName = $fileName;

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
		$uploadedFiles = array_map('basename', $uploadedFiles);

		$elementsToReturn = [];
		foreach ($uploadedFiles as $fileName) {
			$elementsToReturn[] = static::getByName($fileName);
		}

		return array_filter($elementsToReturn);  // Skip "false" boolean values returned by getByName().
	}
}