<?php

/**
* WizyTówka 5
* Admin page — file editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class FileEdit extends __\AdminPanelPage
{
	protected $_pageTitle = 'Edycja pliku';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_FILES;

	private $_file;

	private $_settings;

	protected function _prepare() : void
	{
		if (empty($_GET['name']) or !$this->_file = __\UploadedFile::getByName($_GET['name'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}

		$this->_settings = __\WT()->settings;
	}

	public function POSTQuery() : void
	{
		$newFileName = trim($_POST['newFileName']);

		if ($newFileName and $newFileName != $this->_file->getName()) {
			$newFileName = (new __\Text($newFileName))->makeSlug($this->_settings->filesForceLowercaseNames)->get();

			if (__\UploadedFile::getByName($newFileName)) {
				$this->_HTMLMessage->error('Plik o nazwie „%s” już istnieje.', $newFileName);
			}
			elseif ($this->_file->rename($newFileName)) {
				$this->_HTMLMessage->default('Nazwa pliku została zmieniona.');
				$this->_redirect('fileEdit', ['name' => $newFileName]);
			}
			else {
				$this->_HTMLMessage->error('Podana nazwa pliku jest niepoprawna.');
			}
		}
	}

	protected function _output() : void
	{
		// Replace default admin page title by file name.
		$this->_pageTitle = $this->_file->getName() . ' — edycja pliku';
		$this->_HTMLHead->title('Edycja pliku: „' . $this->_file->getName() . '”');

		$this->_HTMLTemplate->fileFullURL = $this->_settings->websiteAddress . '/' . $this->_file->getURL();
		$this->_HTMLTemplate->fileName    = $this->_file->getName();
		$this->_HTMLTemplate->fileSize    = $this->_file->getSize();
		$this->_HTMLTemplate->fileModTime = $this->_file->getModificationTime();
	}
}