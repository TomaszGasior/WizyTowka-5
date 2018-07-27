<?php

/**
* WizyTówka 5
* HTML template class.
*/
namespace WizyTowka;

class HTMLTemplate implements \IteratorAggregate, \Countable
{
	private $_templatesPath;
	private $_templateName;

	private $_variables = [];

	public function __construct(string $templateName = null, string $templatePath = null)
	{
		$this->_templateName  = (string)$templateName;
		$this->_templatesPath = (string)$templatePath;
	}

	public function __get(string $variable)
	{
		return $this->_variables[$variable];
	}

	public function __set(string $variable, $value) : void
	{
		try {
			$this->_variables[$variable] = $this->_escapeValue($value);
		} catch (\UnexpectedValueException $e) {
			throw HTMLTemplateException::valueCantBeEscaped($variable);
		}
	}

	public function setRaw(string $variable, $value) : void
	{
		$this->_variables[$variable] = $value;  // Don't escape value.
	}

	public function __isset(string $variable) : bool
	{
		return isset($this->_variables[$variable]);
	}

	public function __unset(string $variable) : void
	{
		unset($this->_variables[$variable]);
	}

	public function __debugInfo() : array
	{
		return $this->_variables;
	}

	public function __toString() : string
	{
		ob_start();
		$this->render();
		return ob_get_clean();
	}

	public function getIterator() : iterable // For IteratorAggregate interface.
	{
		foreach ($this->_variables as $key => $value) {
			yield $key => $value;
		}
	}

	public function count() : int // For Countable interface.
	{
		return count($this->_variables);
	}

	public function getTemplate() : ?string
	{
		return $this->_templateName;
	}

	public function setTemplate(?string $templateName) : void
	{
		$this->_templateName = $templateName;
	}

	public function getTemplatePath() : ?string
	{
		return $this->_templatesPath;
	}

	public function setTemplatePath(?string $templatePath) : void
	{
		$this->_templatesPath = $templatePath;
	}

	public function render(string $templateName = null) : void
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

		$include = function($___variables___, $___template___)
		{
			try {
				ob_start();
				extract($___variables___, EXTR_SKIP | EXTR_REFS);
				include $___template___;
				ob_end_flush();
			}
			catch (\Throwable $e) {
				ob_end_clean();
				WT()->errors->addToLog($e);
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

	private function _escapeValue($value)
	{
		switch (gettype($value)) {
			case 'integer':
			case 'double':
			case 'boolean':
			case 'NULL':
				return $value;

			case 'string':
				return HTML::escape($value);

			case 'array':
				return array_map(__METHOD__, $value);

			case 'object':
				if ($value instanceof $this or $value instanceof HTMLTag) {
					return $value;
				}
				elseif ($value instanceof \Traversable) {
					return (object)$this->{__FUNCTION__}(iterator_to_array($value));
				}
				elseif ($value instanceof \stdClass) {
					return (object)$this->{__FUNCTION__}((array)$value);
				}

			default:
				throw new \UnexpectedValueException;
		}
	}
}

class HTMLTemplateException extends Exception
{
	static public function templateNotSpecified()
	{
		return new self('Template name was not specified.', 1);
	}
	static public function valueCantBeEscaped($variable)
	{
		return new self('Value of "' . $variable . '" variable cannot be escaped. Allowed types: integer, float, boolean, array, template instance, iterator, stdClass. Convert variable value or use setRaw() instead.', 2);
	}
}