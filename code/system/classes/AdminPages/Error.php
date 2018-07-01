<?php

/**
* WizyTówka 5
* Admin page — errors page. User is redirected to this page, when error was encountered.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Error extends __\AdminPanelPage
{
	protected $_pageTitle = 'Wystąpił bląd';

	protected function _prepare()
	{
		if (empty($_GET['type'])) {
			$this->_redirect('error', ['type' => 'unknown']);
		}
	}

	protected function _output()
	{
		$messages = [
			'unknown'     => 'Wystąpił błąd, ale nie wiemy jaki… <span style="opacity: 0.5; display: block; font-size: 0.63em">Detektyw Monk powinien się tym zająć.</span>',

			'permissions' => 'Nie posiadasz wystarczających uprawnień do korzystania z&nbsp;tej strony panelu administracyjnego.',
			'parameters'  => 'Podano błędne parametry bądź brak wymaganych parametrów w&nbsp;adresie URL.',
			'lockdown'    => 'Ta funkcja została wyłączona i&nbsp;jest niedostępna.',
		];

		$this->_HTMLTemplate->setTemplate('Message');
		$this->_HTMLTemplate->CSSClasses  = 'iconWarning';
		$this->_HTMLTemplate->messageText = isset($messages[$_GET['type']]) ? $messages[$_GET['type']] : $messages['unknown'];
	}
}