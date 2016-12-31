<?php

/**
* WizyTówka 5
* Admin panel login form.
*/
namespace WizyTowka;

class AP_Login extends Controller  /* Do not extends AdminPanel abstract class. It is separated. */
{
	private $_apTemplate;
	private $_apMessage;
	private $_apMessageError = false;

	public function __construct()
	{
		if (UserSession::isLoggedIn()) {
			$this->_redirect(AdminPanel::URL('pages'));
		}

		$this->_apTemplate = new HTMLTemplate;
	}

	public function POSTQuery()
	{
	}

	public function output()
	{
		$this->_apTemplate->head = new HTMLHead;
		$this->_apTemplate->head->addStyle('AdminMain.css');
		$this->_apTemplate->head->setTitle(Settings::get('websiteTitle') . ' — WizyTówka');

		$this->_apTemplate->message = $this->_apMessage;
		$this->_apTemplate->messageError = $this->_apMessageError;

		$this->_apTemplate->render('AP_Login');
	}
}