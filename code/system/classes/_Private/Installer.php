<?php

/**
* WizyTówka 5
* System installer. Creates base structure of data files, database schema and configuration files.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class Installer
{
	private $_userName;
	private $_userPassword;
	private $_userEmail;

	private $_settings = [];

	public function setUser(string $name, string $password, string $email = null) : void
	{
		$this->_userName     = $name;
		$this->_userPassword = $password;
		$this->_userEmail    = $email;
	}

	public function appendSettings(array $newSettings) : void
	{
		$this->_settings = array_merge($this->_settings, $newSettings);
	}

	public function install() : void
	{
		try {
			// Create structure of data directory.
			$this->_createDataDirStructure();

			// Prepare system settings merged with defaults.
			$this->_prepareSettingsFile();

			// Connect to database and prepare schema.
			$this->_prepareDatabase();

			// Create user with full privileges.
			$this->_createAdministratorUser();

			// Create example content.
			$this->_createExampleContent();
		}
		catch (\Throwable $e) {
			$failed = true;

			// Silently clean up broken data directory after failed installation.
			$this->_cleanUpAfterFail();

			throw $e;
		}
		finally {
			// Send information about installation to project author.
			$this->_sendAddressToAuthor(isset($failed));
		}
	}

	private function _createDataDirStructure() : void
	{
		$mkdir = function($pathname)
		{
			mkdir($pathname, 0777, true);
		};

		$mkdir(__\DATA_DIR);
		$mkdir(__\FILES_DIR);
		$mkdir(__\CONFIG_DIR);

		// Config directory contents.
		__\ConfigurationFile::createNew(__\CONFIG_DIR . '/settings.conf');
		__\ConfigurationFile::createNew(__\CONFIG_DIR . '/sessions.conf');
		touch(__\CONFIG_DIR . '/errors.log');
		file_put_contents(__\CONFIG_DIR . '/.htaccess', 'deny from all');

		// Addons directory.
		$mkdir(__\DATA_DIR . '/addons/plugins');
		$mkdir(__\DATA_DIR . '/addons/types');
		$mkdir(__\DATA_DIR . '/addons/themes');
	}

	private function _prepareSettingsFile() : void
	{
		$defaultSettings = iterator_to_array(
			new __\ConfigurationFile(__\SYSTEM_DIR . '/defaults/settings.conf')
		);

		// These setting have to be set by installer itself.
		$this->_settings['sessionCookiePart']      = rand(100000, 999999);
		$this->_settings['systemInstallationTime'] = time();
		$this->_settings['systemLastUpdateTime']   = time();
		$this->_settings['systemVersion']          = __\VERSION;

		// Check whether all required settings are set by appendSettings(). If setting default value
		// is "__INSTALLER__" it means that this setting have to be set during installation.
		foreach ($defaultSettings as $setting => $value) {
			if ($value === '__INSTALLER__' and empty($this->_settings[$setting])) {
				throw InstallerException::requiredSetting($setting);
			}
		}

		// Website address should not have "/" at the end.
		if (substr($this->_settings['websiteAddress'], -1) == '/') {
			$this->_settings['websiteAddress'] = substr($this->_settings['websiteAddress'], 0, -1);
		}

		// Save settings to file.
		$settingsFile = new __\ConfigurationFile(__\CONFIG_DIR . '/settings.conf');

		foreach (array_merge($defaultSettings, $this->_settings) as $setting => $value) {
			$settingsFile->$setting = $value;
		}
		$settingsFile->save();
	}

	private function _prepareDatabase() : void
	{
		$generateSchema = function($driver)
		{
			$schemaTemplate = explode("\n", file_get_contents(__\SYSTEM_DIR . '/defaults/schema.sql'));
			$schema = '';

			foreach ($schemaTemplate as $line) {
				if (preg_match('/-- wt_dbms: (?<not>! ){0,1}(?<dbms>[a-z]*)/', $line, $matches)) {
					if ($matches['dbms'] != $driver xor $matches['not']) {
						continue;
					}
				}

				$line = preg_replace('/--.*/', '', $line);

				if (trim($line) == '') {
					continue;
				}

				$schema .= rtrim($line) . "\n";
			}

			return $schema;
		};

		if ($this->_settings['databaseType'] == 'sqlite') {
			$pdo = new DatabasePDO('sqlite', __\CONFIG_DIR . '/database.db');
		}
		else {
			$pdo = new DatabasePDO(
				$this->_settings['databaseType'], $this->_settings['databaseName'],
				$this->_settings['databaseHost'], $this->_settings['databaseUsername'], $this->_settings['databasePassword']
			);
		}

		$pdo->exec($generateSchema($this->_settings['databaseType']));

		// Will be needed later to place user data and example page inside database.
		__\WT()->overwrite('database', $pdo);
	}

	private function _createAdministratorUser() : void
	{
		if (!$this->_userName or !$this->_userPassword) {
			throw InstallerException::userNeeded();
		}

		$user = new __\User;

		$user->name = $this->_userName;
		$user->setPassword($this->_userPassword);

		if ($this->_userEmail) {
			$user->email = $this->_userEmail;
		}

		$possibleUserPermissions = array_filter(
			(new \ReflectionClass(__\User::class))->getConstants(),
			function($constantName){ return (strpos($constantName, 'PERM_') === 0); },
			ARRAY_FILTER_USE_KEY
		);
		foreach ($possibleUserPermissions as $value) {
			$user->permissions = $user->permissions | $value;
		}

		$user->save();
	}

	private function _createExampleContent() : void
	{
		$text = <<< HTML_TEXT
<p>Pomyślnie zainstalowano system %s.</p>
<p>Lorem ipsum dolor sit amet wisi. Aenean mollis pulvinar. Pellentesque habitant morbi tristique magna non eros. Suspendisse commodo wisi. Vestibulum ante nec turpis sed leo sed turpis. Lorem ipsum primis in sapien. Sed venenatis. Curabitur sed justo. Vivamus ornare, odio sit amet leo vel tincidunt tellus. Cum sociis natoque penatibus.</p>
<p>Aenean nulla facilisis diam placerat id, dolor. Nullam erat at erat id ligula. Lorem ipsum cursus quis, placerat id, libero. Cras aliquet. Praesent rutrum. Nullam tellus tristique enim, ac elit tincidunt mauris. Aenean congue vel, orci. Nullam et wisi magna, tincidunt augue quis augue. Sed eu sem vel blandit suscipit.</p>
<p>Lorem ipsum dolor ut nonummy id, placerat id, justo. Nulla facilisi. Etiam risus nisl, tristique enim, euismod nec, sagittis vel, ornare sollicitudin. Praesent ac purus. Sed aliquet malesuada, diam ut metus. Aenean congue sit amet, accumsan quam, lobortis magna sit amet augue pulvinar interdum, dolor sit amet, tempor magna. Ut.</p>
HTML_TEXT;

		$page = new __\Page;

		$page->title   = 'Strona główna';
		$page->slug    = 'strona-glowna';
		$page->userId  = 1;
		$page->isDraft = false;
		$page->noIndex = false;

		$page->contentType    = 'PlainText';
		$page->contents->html = sprintf($text, __\VERSION_NAME);

		$page->save();
	}

	private function _sendAddressToAuthor(bool $withFail) : void
	{
		if (defined(__NAMESPACE__ . '\INSTALLER_DONT_SEND_ADDRESS')) {
			return;
		}

		try {
			file_get_contents(
				'https://wizytowka.tomaszgasior.pl/installation?' . http_build_query([
					'address' => $this->_settings['websiteAddress'],
					'version' => __\VERSION,
					'failed'  => (int)$withFail,
				])
			);
		}
		catch (\Throwable $e) {} // Be quiet.
	}

	private function _cleanUpAfterFail() : void
	{
		try {
			$directoryContents = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(__\DATA_DIR, \FilesystemIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
			);
			foreach ($directoryContents as $file) {
				$file->isDir() ? rmdir($file) : unlink($file);
			}

			rmdir(__\DATA_DIR);
		}
		catch (\Throwable $e) {} // Be quiet.
	}
}

class InstallerException extends __\Exception
{
	static public function requiredSetting($setting)
	{
		return new self('System setting "' . $setting . '" cannot be empty.', 1);
	}
	static public function userNeeded()
	{
		return new self('It\'s impossible to install system without user.', 2);
	}
}