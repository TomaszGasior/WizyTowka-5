<?php

/**
* WizyTówka 5
* Admin panel abstract controller.
*/
namespace WizyTowka;

abstract class AdminPanel extends Controller
{
	protected $_pageTitle = 'Panel administracyjny';
	protected $_requiredUserPermissions;
	protected $_template;

	private $_tplLayout;
	private $_tplTopMenu;
	private $_tplMainMenu;

	protected $_tplHead;
	protected $_tplContextMenu;
	protected $_tplMessage;
	protected $_tplMessageError = false;

	public function __construct()
	{
		$this->_prepare();
	}

	public function output()
	{
		$this->_head = new HTMLHead;
		$this->_head->setTitle($this->_pageTitle . ' — WizyTówka');
		$this->_head->addStyle('AdminMain.css');

		$className = substr(strrchr(static::class, '\\'), 1);
		$this->_template = new HTMLTemplate($className);

		$this->_output();

		$this->_tplLayout = new HTMLTemplate('AdminPanelLayout');
		$this->_tplLayout->head = $this->_head;
		$this->_tplLayout->pageTitle = $this->_pageTitle;
		$this->_tplLayout->pageTemplate = $this->_template;
		$this->_tplLayout->message = $this->_tplMessage;
		$this->_tplLayout->messageError = $this->_tplMessageError;
		$this->_tplLayout->render();
	}

	abstract protected function _prepare();
	// Equivalent of __construct() method for child classes.

	abstract protected function _output();
	// Equivalent of output() method for child classes.

	static public function URL($target, $arguments = [])
	{
		if (isset($arguments['c'])) {
			throw new Exception('Argument of admin panel URL must not have key named "c".', 25);
		}
		$arguments['c'] = $target;

		return Settings::get('systemAdminPanelFile') . ($arguments ? '?'.http_build_query($arguments) : '');
	}
}