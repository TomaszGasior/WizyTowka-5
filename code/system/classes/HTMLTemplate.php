<?php

/**
* WizyTówka 5
* HTML template class.
*/
namespace WizyTowka;

class HTMLTemplate implements \IteratorAggregate, \Countable
{
	static private $_autoloaderAdded = false;

	private $_templatesPath;

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

	public function getTemplate()
	{
		return $this->_templateName;
	}

	public function setTemplate($templateName)
	{
		$this->_templateName = $templateName;
	}

	public function getTemplatePath()
	{
		return $this->_templatesPath;
	}

	public function setTemplatePath($templatePath)
	{
		$this->_templatesPath = $templatePath;
	}

	public function render($templateName = null)
	{
		if (!self::$_autoloaderAdded) {
			spl_autoload_register([$this, '_shortHTMLNamesAutoloader']);
			self::$_autoloaderAdded = true;
		}

		if (empty($templateName)) {
			if (empty($this->_templateName)) {
				throw HTMLTemplateException::templateNotSpecified();
			}
			$templateName = $this->_templateName;
		}

		if (!headers_sent()) {
			header('Content-type: text/html; charset=UTF-8');
		}

		$include = function(&$___variables___, $___template___)
		{
			try {
				ob_start();
				extract($___variables___, EXTR_SKIP | EXTR_REFS);
				include $___template___;
				ob_end_flush();
			}
			catch (\Throwable $e) {
				ob_end_clean();
				echo '<br><b>Template rendering error.</b><br>', get_class($e), ': ', $e->getMessage(),
					 '<br>', basename($e->getFile()), ':', $e->getLine(), '<br>';
			}
			catch (\Exception $e) { // PHP 5.6 backwards compatibility.
				ob_end_clean();
				echo '<br><b>Template rendering error.</b><br>', get_class($e), ': ', $e->getMessage(),
					 '<br>', basename($e->getFile()), ':', $e->getLine(), '<br>';
			}
		};
		$include = $include->bindTo(null);
		// Anonymous function is used here to isolate $this and local variables.

		$include(
			$this->_variables,
			(empty($this->_templatesPath) ? null : $this->_templatesPath.'/') . $templateName . '.php'
		);
	}

	// This autoloader is used to make creating new classes easier in templates code.
	// Instead of `new WizyTowka\HTMLFormFields()` it's possible to use shorter `new HTMLFormFields()` syntax.
	private function _shortHTMLNamesAutoloader($classNamePart)
	{
		static $inProgress;  // Avoid endless loop while calling class_exists().

		if ($inProgress) {
			return false;
		}

		$potentialClass = '\\' . __NAMESPACE__ . '\\' . $classNamePart;

		$inProgress  = true;
		$classExists = class_exists($potentialClass);
		$inProgress  = false;

		if ($classExists) {
			class_alias($potentialClass, $classNamePart);
		}

		return $classExists;
	}
}

class HTMLTemplateException extends Exception
{
	static public function templateNotSpecified()
	{
		return new self('Template name was not specified.', 1);
	}
}