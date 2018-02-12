<?php

/**
* WizyTówka 5
* Content management system own exception.
*/
namespace WizyTowka;

abstract class Exception extends \Exception
{
	public function __construct(...$arguments)
	{
		parent::__construct(...$arguments);

		// In this project is used concept of creating exceptions by static factory method in exception class.
		// More here: http://rosstuck.com/formatting-exception-messages/

		// Properties of exception object are set during creating, not during throwing.
		// For example $line property contain number of line, where exception was created, not where was thrown.
		// Code below corrects values of exception properties for better readability.

		if (!$this->getMessage() or !$this->getCode()) {
			trigger_error('Exception must not be thrown without message and code.', E_USER_NOTICE);
		}

		if (isset($this->getTrace()[0]['class']) and $this->getTrace()[0]['class'] == static::class) {
			$this->line = $this->getTrace()[0]['line'];
			$this->file = $this->getTrace()[0]['file'];

			// This dirty hack cleans exception backtrace - removes calling to static factory method in exception class.
			$traceProperty = new \ReflectionProperty(parent::class, 'trace');
			$traceProperty->setAccessible(true);
			$traceProperty->setValue($this, array_slice($traceProperty->getValue($this), 1));

			// Each class should have own exception class with "Exception" sufix. Exception should not be thrown
			// from foreign class. For example "Foo" class must throw only "FooException" delcared in the same file.
			// However, child classes are able to throw exception designed for parent class.
			$designedForClass = str_replace('Exception', null, static::class);
			$thrownFromClass = $this->getTrace()[0]['class'];
			if ($designedForClass != $thrownFromClass and !is_subclass_of($thrownFromClass, $designedForClass)) {
				trigger_error('Exception designed for ' . $designedForClass . ' class must not be thrown from ' . $thrownFromClass . ' class.', E_USER_NOTICE);
			}
		}
		else {
			trigger_error('Exception must not be thrown directly.', E_USER_NOTICE);
		}
	}
}