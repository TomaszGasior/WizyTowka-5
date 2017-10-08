<?php

/**
* WizyTówka 5
* Admin page — errors page. User is redirected to this page, when error was encountered.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Error extends WT\AdminPanel
{
	protected $_pageTitle = 'Wystąpił bląd';

	public function _prepare()
	{
		if (empty($_GET['type'])) {
			$this->_redirect(null);
		}
	}

	protected function _output()
	{
		$messages = [
			'unknowwn'    => 'Wystąpił błąd, ale nie wiemy jaki… <span style="opacity: 0.5; display: block; font-size: 0.63em">Detektyw Monk powinien się tym zająć.</span>',

			'permissions' => 'Nie posiadasz wystarczających uprawnień do korzystania z&nbsp;tej strony panelu administracyjnego.',
			'parameters'  => 'Podano błędne parametry bądź brak wymaganych parametrów w&nbsp;adresie URL.',
		];

		$this->_apTemplate->setTemplate('Message');
		$this->_apTemplate->CSSClasses  = 'iconWarning';
		$this->_apTemplate->messageText = isset($messages[$_GET['type']]) ? $messages[$_GET['type']] : $messages['unknowwn'];
	}
}