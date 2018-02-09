<?php

/**
* WizyTówka 5
* Admin page — file editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class FileEdit extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Edycja pliku';
	protected $_userRequiredPermissions = WT\User::PERM_MANAGE_FILES;

	private $_file;

	protected function _prepare()
	{
		if (empty($_GET['name']) or !$this->_file = WT\UploadedFile::getByName($_GET['name'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}
	}

	public function POSTQuery()
	{
		$_POST['newFileName'] = trim($_POST['newFileName']);

		if (!empty($_POST['newFileName']) and $_POST['newFileName'] != $this->_file->getName()) {
			$newFileName = (new WT\Text($_POST['newFileName']))->makeSlug(WT\Settings::get('filesForceLowercaseNames'))->get();

			if (WT\UploadedFile::getByName($newFileName)) {
				$this->_HTMLMessage->error('Plik o nazwie „' . $newFileName . '” już istnieje.');
			}
			elseif ($this->_file->rename($newFileName)) {
				$this->_redirect('fileEdit', ['name' => $newFileName, 'msg' => 1]);
			}
			else {
				$this->_HTMLMessage->error('Podana nazwa pliku jest niepoprawna.');
			}
		}
	}

	protected function _output()
	{
		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->default('Nazwa pliku została zmieniona.');
		}

		$this->_HTMLTemplate->fileName    = $this->_file->getName();
		$this->_HTMLTemplate->fileFullURL = WT\Settings::get('websiteAddress') . '/' . $this->_file->getURL();
	}
}