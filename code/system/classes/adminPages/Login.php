<?php

/**
* WizyTówka 5
* Admin page — login form.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Login extends WT\Controller  /* Does not extends AdminPanel abstract class, it is separated. */
{
	private $_apTemplate;
	private $_apMessage;
	private $_apMessageError = false;

	public function __construct()
	{
		if (WT\SessionManager::isUserLoggedIn()) {
			$this->_redirect(AdminPanel::URL('pages'));
		}

		$this->_apTemplate = new WT\HTMLTemplate('Login', WT\SYSTEM_DIR.'/templates/adminPages');
	}

	public function POSTQuery()
	{
	}

	public function output()
	{
		$this->_apTemplate->head = new WT\HTMLHead;
		$this->_apTemplate->head->setTitle(WT\Settings::get('websiteTitle') . ' — WizyTówka');
		$this->_apTemplate->head->setAssetsPath(basename(WT\SYSTEM_DIR).'/assets');
		$this->_apTemplate->head->addStyle('AdminMain.css');

		$this->_apTemplate->message = $this->_apMessage;
		$this->_apTemplate->messageError = $this->_apMessageError;

		$this->_apTemplate->render();
	}
}