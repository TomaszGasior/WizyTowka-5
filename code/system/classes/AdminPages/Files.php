<?php

/**
* WizyTówka 5
* Admin page — files.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Files extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Wysłane pliki';
	protected $_userRequiredPermissions = WT\User::PERM_MANAGE_FILES;

	private $_files;

	protected function _prepare()
	{
		if (!empty($_GET['deleteName'])) {
			$this->_deleteFile($_GET['deleteName']);
		}

		$this->_files = WT\UploadedFile::getAll();
	}

	private function _deleteFile($name)
	{
		if ($file = WT\UploadedFile::getByName($name)) {
			$file->delete();
			$this->_HTMLMessage->success('Plik „' . WT\HTML::escape($name) . '” został usunięty.');
		};
	}

	protected function _output()
	{
		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->success('Przesyłanie zostało zakończone pomyślnie.');
		}

		$files = [];
		foreach ($this->_files as $file) {
			$files[] = (object)[
				'name' => $file->getName(),
				'size' => $file->getSize(),
				'url'  => $file->getURL(),
			];
		}
		$this->_HTMLTemplate->files = $files;
	}
}