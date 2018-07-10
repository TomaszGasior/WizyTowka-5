<?php

/**
* WizyTówka 5
* Admin page — send file(s).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class FilesSend extends __\AdminPanelPage
{
	use FilesSendingCommon;

	protected $_pageTitle = 'Wyślij pliki';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_FILES;

	public function POSTQuery() : void
	{
		$this->_handleSentFiles();

		// Redirect to sent files list only when sending operation was without problems.
		if (!$this->_sendingErrors) {
			$this->_HTMLMessage->success('Przesyłanie zostało zakończone pomyślnie.');
			$this->_redirect('files');
		}
	}

	protected function _output() : void
	{
		$this->_HTMLTemplate->errorsList = [];

		// Show ease to read error message to user.
		// * Show standard top error message if only one file was sent and one error was encountered.
		// * If more files was sent, show general information about problem as top message and detailed information as list.
		// * Tell user whether all files weren't send properly or only part of these.
		if ($this->_sendingErrors) {
			if ($this->_sendingCount = 1) {
				$this->_HTMLMessage->error(array_values($this->_sendingErrors)[0]);
			}
			else {
				$this->_HTMLTemplate->errorsList = $this->_sendingErrors;

				$this->_HTMLMessage->error(
					($this->_sendingCount == count($this->_sendingErrors))
					? 'Wystąpiły błędy podczas przesyłania plików. Żaden plik nie został przesłany.'
					: 'Wystąpiły błędy podczas przesyłania niektórych plików. Część plików została przesłana pomyślnie.'
				);
			}
		}

		$this->_HTMLTemplate->maxFileSize     = $this->_getMaxFileSize(); // Hidden input "MAX_FILE_SIZE" for PHP.
		$this->_HTMLTemplate->maxFilesNumber  = $this->_getMaxFilesNumber();
		$this->_HTMLTemplate->featureDisabled = !$this->_isSendingFilesEnabled();
	}
}