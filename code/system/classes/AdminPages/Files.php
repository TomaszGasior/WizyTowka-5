<?php

/**
* WizyTówka 5
* Admin page — files.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Files extends __\AdminPanelPage
{
	protected $_pageTitle = 'Wysłane pliki';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_FILES;

	private $_files;

	protected function _prepare() : void
	{
		if (!empty($_GET['deleteName'])) {
			$this->_deleteFile($_GET['deleteName']);
		}

		$this->_files = __\UploadedFile::getAll();
	}

	private function _deleteFile(string $name) : void
	{
		if ($file = __\UploadedFile::getByName($name)) {
			$file->delete();
			$this->_HTMLMessage->success('Plik „%s” został usunięty.', $name);
		};
	}

	protected function _output() : void
	{
		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->success('Przesyłanie zostało zakończone pomyślnie.');
		}

		$this->_HTMLContextMenu->append('Wyślij pliki', self::URL('filesSend'), 'iconAdd');

		$files = [];
		foreach ($this->_files as $file) {
			$files[] = (object)[
				'name'    => __\HTML::escape($file->getName()),
				'rawName' => $file->getName(), // Raw file name is needed for admin pages URLs.
				'size'    => $file->getSize(),
				'url'     => $file->getURL(),
			];
		}
		$this->_HTMLTemplate->setRaw('files', $files);
	}
}