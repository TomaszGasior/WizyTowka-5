<?php

/**
* WizyTówka 5
* Admin page — files.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Files extends WT\AdminPanel
{
	protected $_pageTitle = 'Wysłane pliki';

	private $_files;

	protected function _prepare()
	{
		if (!empty($_GET['deleteId'])) {
			$this->_deleteFile($_GET['deleteId']);
		}

		$this->_files = WT\File::getAll();
	}

	private function _deleteFile($fileId)
	{
		if ($file = WT\File::getById($fileId)) {
			$file->delete();
			$this->_HTMLMessage->success('Plik „' . $file->name . '” został usunięty.');
		};
	}

	protected function _output()
	{
		$this->_HTMLTemplate->files = $this->_files;
	}
}