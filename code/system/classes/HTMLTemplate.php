<?php

/**
* WizyTówka 5
* HTML template class.
*/
namespace WizyTowka;

class HTMLTemplate implements \IteratorAggregate, \Countable
{
	private $_templatesPath = SYSTEM_DIR . '/templates';

	private $_variables = [];
	private $_templateName;

	public function __construct($templateName = null, $templatePath = null)
	{
		$this->_templateName = $templateName;
		if ($templatePath) {
			$this->_templatesPath = $templatePath;
		}
	}

	public function __get($variable)
	{
		return $this->_variables[$variable];
	}

	public function __set($variable, $value)
	{
		$this->_variables[$variable] = $value;
	}

	public function __isset($variable)
	{
		return isset($this->_variables[$variable]);
	}

	public function __unset($variable)
	{
		unset($this->_variables[$variable]);
	}

	public function __debugInfo()
	{
		return $this->_variables;
	}

	public function __toString()
	{
		ob_start();
		$this->render();
		return ob_get_clean();
	}

	public function &getIterator() // For IteratorAggregate interface.
	{
		foreach ($this->_variables as $key => &$value) {
			yield $key => $value;
		}
		// Reference is used to allow foreach syntax like it: foreach($object as &$value) { ... }.
	}

	public function count() // For Countable interface.
	{
		return count($this->_variables);
	}

	public function setTemplate($templateName)
	{
		$this->_templateName = $templateName;
	}

	public function setTemplatePath($templatePath)
	{
		$this->_templatesPath = $templatePath;
	}

	public function render($templateName = null)
	{
		if (empty($templateName)) {
			if (empty($this->_templateName)) {
				throw HTMLTemplateException::templateNotSpecified();
			}
			$templateName = $this->_templateName;
		}

		if (!headers_sent()) {
			header('Content-type: text/html; charset=UTF-8');
		}

		$include = function(&$___variables___, $___template___) {
			try {
				ob_start();
				extract($___variables___, EXTR_SKIP | EXTR_REFS);
				include $___template___;
				ob_end_flush();
			}
			catch (\Exception $e) {  // Throwable should be used in PHP 7.
				ob_end_clean();
				echo '<br><b>Template rendering error.</b><br>',
					 'Message: ', $e->getMessage(), '<br>',
					 'File: ', basename($e->getFile()), ':', $e->getLine(), '<br>';
			}
		};
		$include = $include->bindTo(null);
		// Anonymous function is used here to isolate $this and local variables.

		$include(
			$this->_variables,
			(empty($this->_templatesPath) ? : $this->_templatesPath.'/') . $templateName . '.php'
		);
	}
}

class HTMLTemplateException extends Exception
{
	static public function templateNotSpecified()
	{
		return new self('Template name was not specified.', 1);
	}
}