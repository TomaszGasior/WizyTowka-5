<?php

/**
* WizyTówka 5
* Content management system own exception.
*/
namespace WizyTowka;

class Exception extends \Exception
{
	public function __construct(...$arguments)
	{
		call_user_func_array([parent::class, '__construct'], $arguments);

		if (!$this->getMessage() or !$this->getCode()) {
			trigger_error('You should not throw exception without message and code.', E_USER_NOTICE);
		}

		if (isset($this->getTrace()[0]['class']) and $this->getTrace()[0]['class'] == static::class) {
			// In this project is used concept of creating exceptions by static factory method in exception class.
			// More informations here: http://rosstuck.com/formatting-exception-messages/
			// Properties of exception object are set during creating, not during throwing, so for example $line property contain number of line,
			// where exception was created, not where was thrown. Code below corrects values of exception properties.
			$this->line = $this->getTrace()[0]['line'];
			$this->file = $this->getTrace()[0]['file'];

			// This dirty hack cleans exception backtrace, removes calling to static factory method in exception class.
			$traceProperty = new \ReflectionProperty(parent::class, 'trace');
			$traceProperty->setAccessible(true);
			$traceProperty->setValue($this, array_slice($traceProperty->getValue($this), 1));

			$designedForClass = str_replace('Exception', null, static::class);
			$thrownFromClass = $this->getTrace()[0]['class'];
			if ($designedForClass != $thrownFromClass and !is_subclass_of($thrownFromClass, $designedForClass)) {
				trigger_error('You should not throw exception designed for other class.', E_USER_NOTICE);
			}
		}
		else {
			trigger_error('You should not throw exceptions directly.', E_USER_NOTICE);
		}
	}
}