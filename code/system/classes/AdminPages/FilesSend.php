<?php

/**
* WizyTówka 5
* Admin page — send file(s).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class FilesSend extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Wyślij pliki';
	protected $_userRequiredPermissions = WT\User::PERM_SENDING_FILES;

	private $_errorMessages = [
		'moveUploadedFileFailed'    => 'Przenoszenie pliku z folderu tymczasowego nie powiodło się.',
		'maybePOSTRequestFailed'    => 'Przesyłanie nie powiodło się (prawdopodobne błędne żądanie POST).',
		'fileExistsWithChangedName' => 'Jakimś cudem plik o tej samej nazwie już istnieje.',
		'unknownPHPError'           => 'Wystąpił błąd PHP podczas przesyłania pliku.',
	];
	private $_PHPErrorMessages = [
		\UPLOAD_ERR_INI_SIZE   => 'Przesłany plik jest zbyt duży (rozmiar określa konfiguracja PHP).',
		\UPLOAD_ERR_FORM_SIZE  => 'Przesłany plik jest zbyt duży.',
		\UPLOAD_ERR_PARTIAL    => 'Plik został przesłany tylko częściowo.',
		\UPLOAD_ERR_NO_FILE    => 'Nie przesłano żadnego pliku.',
		\UPLOAD_ERR_NO_TMP_DIR => 'Nie określono folderu tymczasowego do przesyłania plików.',
		\UPLOAD_ERR_CANT_WRITE => 'Zapis przesyłanego pliku do folderu tymczasowego jest niemożliwy.',
		\UPLOAD_ERR_EXTENSION  => 'Przesyłanie pliku nie powiodło się z powodu rozszerzenia PHP.',
	];

	// This array should contain file names as keys and error messages as values.
	// Sending was successful if this array is empty.
	private $_sendingErrors = [];

	// Amount of sent files.
	private $_sendingCount = 0;

	public function POSTQuery()
	{
		if (empty($_FILES['sendingFiles']['name']) or !is_array($_FILES['sendingFiles']['name'])) {
			$this->_sendingErrors[''] = $this->_errorMessages['maybePOSTRequestFailed'];
			return;
		}

		$this->_sendingCount = count($_FILES['sendingFiles']['name']);

		foreach ($_FILES['sendingFiles']['name'] as $key => $originalFileName) {
			$temporaryFilePath = $_FILES['sendingFiles']['tmp_name'][$key];
			$errorCode         = $_FILES['sendingFiles']['error'][$key];
			$safeFileName      = (new WT\Text($originalFileName))->makeSlug(WT\Settings::get('filesForceLowercaseNames'))->get();

			// Avoid overwriting of existing files. Try to append unix timestamp to file name or skip.
			if (WT\UploadedFile::getByName($safeFileName)) {
				// "example.file.name.png" --> "example.file.name_1517779327.png"
				$safeFileNameParts = array_reverse(explode('.', $safeFileName));
				$safeFileNameParts[1] .= '_' . time();
				$safeFileName = implode('.', array_reverse($safeFileNameParts));

				if (WT\UploadedFile::getByName($safeFileName)) {
					$this->_sendingErrors[$safeFileName] = $this->_errorMessages['fileExistsWithChangedName'];
					continue;
				}
			}

			// Skip sent file if PHP encountered error.
			if (!empty($errorCode)) {
				$this->_sendingErrors[$safeFileName] = isset($this->_PHPErrorMessages[$errorCode])
				                                       ? $this->_PHPErrorMessages[$errorCode]
				                                       : $this->_errorMessages['unknownPHPError'];
				continue;
			}

			$desitationFilePath = WT\FILES_DIR . '/' . $safeFileName;

			try {
				$moveUploadedFileResult = move_uploaded_file($temporaryFilePath, $desitationFilePath);
			} catch (\ErrorException $e) {
				$moveUploadedFileResult = false;
				WT\ErrorHandler::addToLog($e);
			}

			// Skip if move_uploaded_file() failed.
			if (!$moveUploadedFileResult) {
				$this->_sendingErrors[$safeFileName] = $this->_errorMessages['moveUploadedFileFailed'];
				continue;
			}
		}

		// Redirect to sent files list only when sending operation was without problems.
		if (!$this->_sendingErrors) {
			$this->_redirect('files', ['msg' => 1]);
		}
	}

	protected function _output()
	{
		$this->_HTMLTemplate->errorsList = [];

		// Show ease to read error message to user.
		// * Show standard top error message if only one file was sent and one error was encountered.
		// * If more files was sent, show general information about problem as top message and detailed information as list.
		// * Tell user whether all files weren't send properly or only part of these.
		if ($this->_sendingErrors) {
			if ($this->_sendingCount <= 1) {
				$this->_HTMLMessage->error(array_values($this->_sendingErrors)[0]);
			}
			else {
				$this->_HTMLTemplate->errorsList = $this->_sendingErrors;

				$this->_HTMLMessage->error(
					($this->_sendingCount == count($this->_sendingErrors))
					? 'Wystąpiły błędy podczas przesyłania plików. Żaden plik nie został przesłany.'
					: 'Wystąpiły błędy podczas przesyłania niektórych plików. Część została przesłana pomyślnie.'
				);
			}
		}

		// Hidden input "MAX_FILE_SIZE" for PHP sending logic.
		$this->_HTMLTemplate->maxFileSize = WT\Settings::get('filesSentMaximumSizeBytes');
	}
}