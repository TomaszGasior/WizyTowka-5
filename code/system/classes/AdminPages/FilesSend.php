<?php

/**
* WizyTówka 5
* Admin page — send file(s).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class FilesSend extends __\AdminPanelPage
{
	protected $_pageTitle = 'Wyślij pliki';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_FILES;

	private $_handler;

	protected function _prepare() : void
	{
		$settings = __\WT()->settings;

		$this->_handler = new __\UploadedFilesHandler(
			$settings->filesSentMaximumSizeBytes, $settings->filesForceLowercaseNames
		);
	}

	public function POSTQuery() : void
	{
		if (!is_array($_FILES['sendingFiles'] ?? null)) {
			$this->_HTMLMessage->error('Przesyłanie nie powiodło się (prawdopodobne błędne żądanie POST).');
			return;
		}

		$this->_handler->handleSentFiles($_FILES['sendingFiles']);

		// Redirect to sent files list only when sending operation was without problems.
		if ($this->_handler->countErrors() === 0) {
			$this->_HTMLMessage->success('Przesyłanie zostało zakończone pomyślnie.');
			$this->_redirect('files');
		}
	}

	protected function _output() : void
	{
		$this->_HTMLTemplate->maxFileSize     = $this->_handler->getMaxFileSize();  // Hidden input "MAX_FILE_SIZE" for PHP.
		$this->_HTMLTemplate->maxFilesNumber  = $this->_handler->getMaxFilesNumber();
		$this->_HTMLTemplate->featureDisabled = !$this->_handler->isSendingFilesEnabled();

		$this->_HTMLTemplate->errorsList = [];

		// Show ease to read error message.
		// * Show standard top error message if only one file was sent and one error was encountered.
		// * If more files was sent, show general information about problem as top message and detailed information as list.
		// * Tell user whether all files weren't send properly or only part of these.
		if ($errorsCount = $this->_handler->countErrors()) {
			if ($errorsCount == 1) {
				$firstError = array_values($this->_getErrorsMessages())[0];
				$this->_HTMLMessage->error($firstError);
			}
			else {
				$this->_HTMLTemplate->errorsList = $this->_getErrorsMessages();

				$this->_HTMLMessage->error(
					($this->_handler->countMoved() === 0)
					? 'Wystąpiły błędy podczas przesyłania plików. Żaden plik nie został przesłany.'
					: 'Wystąpiły błędy podczas przesyłania niektórych plików. Część plików została przesłana pomyślnie.'
				);
			}
		}
	}

	// Returns array with file names as keys and error messages as values.
	private function _getErrorsMessages() : array
	{
		$messages = [
	        __\UploadedFilesHandler::ERROR_MOVE_UPLOADED_FILE => 'Przenoszenie pliku z folderu tymczasowego nie powiodło się.',
	        __\UploadedFilesHandler::ERROR_FILE_STILL_EXISTS  => 'Plik o tej samej nazwie już istnieje pomimo próby zmiany nazwy.',
	        __\UploadedFilesHandler::ERROR_FILE_TOO_BIG       => 'Przesłany plik jest zbyt duży.',

	        \UPLOAD_ERR_INI_SIZE   => 'Przesłany plik jest zbyt duży (rozmiar określa konfiguracja PHP).',
	        \UPLOAD_ERR_FORM_SIZE  => 'Przesłany plik jest zbyt duży.',
	        \UPLOAD_ERR_PARTIAL    => 'Plik został przesłany tylko częściowo.',
	        \UPLOAD_ERR_NO_FILE    => 'Nie przesłano żadnego pliku.',
	        \UPLOAD_ERR_NO_TMP_DIR => 'Nie określono folderu tymczasowego do przesyłania plików.',
	        \UPLOAD_ERR_CANT_WRITE => 'Zapis przesyłanego pliku do folderu tymczasowego jest niemożliwy.',
	        \UPLOAD_ERR_EXTENSION  => 'Przesyłanie pliku nie powiodło się z powodu rozszerzenia PHP.',

	        'unknown' => 'Wystąpił błąd PHP podczas przesyłania pliku.',
		];

		$errors = $this->_handler->getErrors();

		foreach ($errors as $fileName => $errorCode) {
			$errors[$fileName] = $messages[$errorCode] ?? $messages['unknown'];
		}

		return $errors;
	}
}