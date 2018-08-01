<?php

/**
* WizyTówka 5
* Installer controller.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class InstallationWizard extends __\Controller
{
	private $_HTMLMessage;
	private $_currentStep = 1;

	public function __construct()
	{
		// Redirect to "admin.php" if user loaded "index.php" or when query string exists.
		if (strpos($_SERVER['REQUEST_URI'], 'admin.php') === false or $_SERVER['QUERY_STRING'] ?? null) {
			header('Location: admin.php');
			exit;
		}

		$this->_HTMLMessage = new __\HTMLMessage;
	}

	public function POSTQuery() : void
	{
		// Wizard steps:
		// * 1 — welcome message,
		// * 2 — license agreement,
		// * 3 — installation settings,
		// * 4 — ending message.

		$this->_currentStep = (int)$_POST['step'];

		switch ($_POST['step']) {
			// After welcome message.
			case 1:
				if (!is_writable(__\PUBLIC_DIR)) {
					$this->_HTMLMessage->error('Główny katalog nie jest zapisywalny.');
					return;
				}
				break;

			// After license agreement.
			case 2:
				if (!isset($_POST['licenseAccepted'])) {
					$this->_HTMLMessage->error('Musisz zaakceptować postanowienia licencyjne.');
					return;
				}
				if (!isset($_POST['sendAddressForStats'])) {
					$this->_HTMLMessage->error('Zezwól na przesłanie adresu twojej strony autorowi WizyTówki. W&nbsp;ten&nbsp;sposób pomożesz mu w pracach nad kolejnymi wersjami systemu.');
					return;
				}
				break;

			// After installation settings.
			case 3:
				// Check whether all form fields are filled in.
				$requiredFields = [
					'websiteTitle', 'websiteAddress', 'userName', 'userPasswordText_1', 'userPasswordText_2', 'userEmail',
					'databaseType', 'errorsVisibility'
				];
				if (($_POST['databaseType'] ?? null) != 'sqlite') {
					$requiredFields = array_merge($requiredFields, ['databaseHost', 'databaseName', 'databaseUsername']);
				}
				foreach ($requiredFields as $key) {
					if (empty($_POST[$key])) {
						$this->_HTMLMessage->error('Nie wypełniono wymaganych pól.');
						return;
					}
				}

				// Is username correct?
				// Keep in sync with AdminPages\UserEditCreateCommon::_checkUserName().
				if (!preg_match('/^[a-zA-Z0-9_\-.]+$/', $_POST['userName'])) {
					$this->_HTMLMessage->error('Nazwa użytkownika jest niepoprawna.');
					return;
				}

				// Is password correct?
				if ($_POST['userPasswordText_1'] !== $_POST['userPasswordText_2']) {
					$this->_HTMLMessage->error('Podane hasła nie są identyczne.');
					return;
				}

				// Try to connect to database service.
				if ($_POST['databaseType'] != 'sqlite') {
					try {
						new DatabasePDO(
							$_POST['databaseType'], $_POST['databaseName'],
							$_POST['databaseHost'], $_POST['databaseUsername'], $_POST['databasePassword']
						);
					} catch (\PDOException $e) {
						$this->_HTMLMessage->error('Błąd połączenia z usługą bazy danych:<br> „%s”.', $e->getMessage());
						return;
					}
				}

				// If everything is checked, do the right job!
				$this->_doInstallation();
				break;

			default:
				$this->_currentStep = 1;
				return;
		}

		// Go to next step.
		++$this->_currentStep;
	}

	public function output() : void
	{
		// HTML <head>.
		$HTMLHead = new __\HTMLHead;
		$HTMLHead->setAssetsPathBase($this->_getDefaultWebsiteAddress());
		$HTMLHead->setAssetsPath(__\SYSTEM_URL . '/assets');
		$HTMLHead->title(__\VERSION_NAME);
		$HTMLHead->meta('viewport', 'width=device-width');
		$HTMLHead->stylesheet('AdminMain.css');
		$HTMLHead->stylesheet('AdminIcons.css');
		$HTMLHead->stylesheet('https://fonts.googleapis.com/css?family=Lato:400,700&subset=latin-ext');

		// Installer template.
		$HTMLTemplate = new __\HTMLTemplate('InstallationWizard', __\SYSTEM_DIR . '/templates');
		$HTMLTemplate->formFields = __\HTMLFormFields::class;
		$HTMLTemplate->step = $this->_currentStep;

		switch ($this->_currentStep) {
			// Welcome message.
			case 1:
				$HTMLTemplate->PHPVersion         = \PHP_VERSION;
				$HTMLTemplate->serverSoftware     = $_SERVER['SERVER_SOFTWARE'] ?? '';
				$HTMLTemplate->isDirWritable      = is_writable(__\PUBLIC_DIR);
				$HTMLTemplate->betaVersionWarning = !__\VERSION_STABLE;
				break;

			// License agreement.
			case 2:
				$HTMLTemplate->licenseText = new __\HTMLTemplate('License', __\SYSTEM_DIR . '/templates');
				break;

			// Installation settings.
			case 3:
				$HTMLTemplate->websiteTitle     = $_POST['websiteTitle']     ?? '';
				$HTMLTemplate->websiteAddress   = $_POST['websiteAddress']   ?? $this->_getDefaultWebsiteAddress();
				$HTMLTemplate->userName         = $_POST['userName']         ?? '';
				$HTMLTemplate->userEmail        = $_POST['userEmail']        ?? '';
				$HTMLTemplate->databaseType     = $_POST['databaseType']     ?? 'sqlite';
				$HTMLTemplate->databaseHost     = $_POST['databaseHost']     ?? 'localhost';
				$HTMLTemplate->databaseName     = $_POST['databaseName']     ?? '';
				$HTMLTemplate->databaseUsername = $_POST['databaseUsername'] ?? '';
				$HTMLTemplate->errorsVisibility = $_POST['errorsVisibility'] ?? 'none';
				break;

			// Ending message.
			case 4:
				break;
		}

		// Main HTML layout.
		$HTMLLayout = new __\HTMLTemplate('AdminPanelAlternative', __\SYSTEM_DIR . '/templates');
		$HTMLLayout->head = $HTMLHead;
		$HTMLLayout->message = $this->_HTMLMessage;
		$HTMLLayout->pageTitle = 'Instalacja — krok ' . $this->_currentStep . '/4';
		$HTMLLayout->pageTemplate = $HTMLTemplate;

		// Recursively render all HTML elements and whole layout.
		$HTMLLayout->render();
	}

	static public function URL($target, array $arguments = []) : ?string
	{
		return null;
	}

	private function _doInstallation()
	{
		$installer = new Installer;

		$installer->appendSettings([
			'databaseType'        => $_POST['databaseType'],
			'websiteAddress'      => $_POST['websiteAddress'],
			'websiteAuthor'       => $_POST['userName'],
			'websiteEmailAddress' => $_POST['userEmail'],
			'websiteTitle'        => $_POST['websiteTitle'],
			'websiteTitlePattern' => '%s — ' . $_POST['websiteTitle'],
		]);
		if ($_POST['databaseType'] != 'sqlite') {
			$installer->appendSettings([
				'databaseHost'     => $_POST['databaseHost'],
				'databaseName'     => $_POST['databaseName'],
				'databasePassword' => $_POST['databasePassword'],
				'databaseUsername' => $_POST['databaseUsername'],
			]);
		}
		if ($_POST['errorsVisibility'] == 'always') {
			$installer->appendSettings(['systemShowErrors' => true]);
		}
		elseif ($_POST['errorsVisibility'] == 'admin') {
			$installer->appendSettings(['adminPanelForceShowErrors' => true]);
		}

		$installer->setUser($_POST['userName'], $_POST['userPasswordText_1'], $_POST['userEmail']);

		$installer->install();
	}

	private function _getDefaultWebsiteAddress() : string
	{
		$address = 'http://';

		if (!empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] != 'off') {
			$address = 'https://';
		}

		$address .= $_SERVER['SERVER_NAME'];

		if (!empty($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] != 80 and $_SERVER['SERVER_PORT'] != 443) {
			$address .= ':' . $_SERVER['SERVER_PORT'];
		}

		if (dirname($_SERVER['SCRIPT_NAME']) != '/') {
			$address .= dirname($_SERVER['SCRIPT_NAME']);
		}

		return $address;
	}
}