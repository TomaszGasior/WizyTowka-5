<?php

/**
* WizyTówka 5
* Common code for FilesSend controller.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

trait FilesSendingCommon
{
	// Important for classes importing this trait. HTML template have to:
	// * use `<form>` tag with `enctype="multipart/form-data"` attribute,
	// * specify `<input type="hidden" name="MAX_FILE_SIZE">` field with _getMaxFileSize() as value,
	// * specify `<input name="sendingFiles[]" type="file" multiple>` field.


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

	// Amount of sent (successfully or not) files.
	private $_sendingCount = 0;

	// This method should be called inside POSTQuery().
	private function _handleSentFiles()
	{
		// Stop if POST request is broken. Maybe request size exceeded maximum size allowed in php.ini?
		if (empty($_FILES['sendingFiles']['name']) or !is_array($_FILES['sendingFiles']['name'])) {
			$this->_sendingErrors[''] = $this->_errorMessages['maybePOSTRequestFailed'];
			$this->_sendingCount = 1;   // Fake value.
			return;
		}

		// Stop if $_POST['MAX_FILE_SIZE'] was changed client-side by user and it's incorrect.
		if (empty($_POST['MAX_FILE_SIZE']) or $_POST['MAX_FILE_SIZE'] > $this->_getMaxFileSize()) {
			$this->_redirect('error');
		}

		$this->_sendingCount = count($_FILES['sendingFiles']['name']);

		foreach ($_FILES['sendingFiles']['name'] as $key => $fileName) {
			$tempFilePath = $_FILES['sendingFiles']['tmp_name'][$key];
			$errorCode    = $_FILES['sendingFiles']['error'][$key];
			$safeFileName = (new WT\Text($fileName))->makeSlug(WT\Settings::get('filesForceLowercaseNames'))->get();

			// Avoid overwriting of existing files. Try to append unix timestamp to file name.
			if (WT\UploadedFile::getByName($safeFileName)) {
				// "example.file.name.png" --> "example.file.name_1517779327.png"
				$safeFileNameParts = array_reverse(explode('.', $safeFileName));
				$safeFileNameParts[1] .= '_' . time();
				$safeFileName = implode('.', array_reverse($safeFileNameParts));

				// Still file exists!? Skip.
				if (WT\UploadedFile::getByName($safeFileName)) {
					$this->_sendingErrors[$safeFileName] = $this->_errorMessages['fileExistsWithChangedName'];
					continue;
				}
			}

			// Skip if PHP encountered error.
			// Maximum size of file set by CMS settings is handled here by PHP and $_POST['MAX_FILE_SIZE'].
			if (!empty($errorCode)) {
				$this->_sendingErrors[$safeFileName] = isset($this->_PHPErrorMessages[$errorCode])
				                                       ? $this->_PHPErrorMessages[$errorCode]
				                                       : $this->_errorMessages['unknownPHPError'];
				continue;
			}

			// Try to move uploaded file to proper place.
			try {
				$desitationFilePath     = WT\FILES_DIR . '/' . $safeFileName;
				$moveUploadedFileResult = move_uploaded_file($tempFilePath, $desitationFilePath);
			} catch (\ErrorException $e) {
				$moveUploadedFileResult = false;
				WT\ErrorHandler::addToLog($e);
			}

			// Skip if move_uploaded_file() failed.
			if (!$moveUploadedFileResult) {
				$this->_sendingErrors[$safeFileName] = $this->_errorMessages['moveUploadedFileFailed'];
			}
		}
	}

	// Returns maximum allowed size of sent files according to php.ini and CMS settings.
	private function _getMaxFileSize()
	{
		// This function parses PHP's shorthand bytes syntax.
		// More here: http://php.net/manual/en/faq.using.php#faq.using.shorthandbytes
		$parseIniOption = function($name)
		{
			$factors = [
				'K' => 1024,
				'M' => 1048576,
				'G' => 1073741824,
			];
			$value   = strtoupper((string)ini_get($name));
			$number  = preg_replace('/[^\-0-9]/', null, $value);
			$unit    = preg_replace('/[^KMG]/', null, $value);

			if (is_numeric($number) and isset($factors[$unit])) {
				$number *= $factors[$unit];
			}

			return $number;
		};

		$possibleValues = array_filter([
			$parseIniOption('post_max_size'),
			$parseIniOption('upload_max_filesize'),
			WT\Settings::get('filesSentMaximumSizeBytes'),
		], function($value){ return $value > 0; });

		return $possibleValues ? min($possibleValues) : 0;
	}

	// Returns maximum amount of sent files.
	private function _getMaxFilesNumber()
	{
		return (ini_get('max_file_uploads') > 0) ? ini_get('max_file_uploads') : 0;
	}

	// Returns true if file sending feature is enabled in PHP configuration.
	private function _isSendingFilesEnabled()
	{
		return (ini_get('file_uploads') and ini_get('max_file_uploads') > 0);
	}
}