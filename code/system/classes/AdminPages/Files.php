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
	protected $_userRequiredPermissions = WT\User::PERM_SENDING_FILES;

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
			$this->_HTMLMessage->success('Plik „' . $name . '” został usunięty.');
		};
	}

	protected function _output()
	{
		$this->_HTMLTemplate->files = $this->_files;
	}
}