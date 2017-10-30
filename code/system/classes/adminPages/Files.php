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
		if (!empty($_GET['deleteId']) and $file = WT\File::getById($_GET['deleteId'])) {
			$file->delete();
			$this->_HTMLMessage->success('Plik „' . $file->name . '” został usunięty.');
		}

		$this->_files = WT\File::getAll();
	}

	protected function _output()
	{
		$this->_HTMLTemplate->files = $this->_files;
	}
}