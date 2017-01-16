<?php

/**
* WizyTówka 5
* Hooks class. Manages actions and filters. Concept inspired by WordPress hooks.
*/
namespace WizyTowka;

class Hooks
{
	static private $_actions = [];
	static private $_filters = [];

	static public function addAction($name, callable $callback)
	{
		self::_addHook(self::$_actions, $name, $callback);
	}

	static public function addFilter($name, callable $callback)
	{
		self::_addHook(self::$_filters, $name, $callback);
	}

	static private function _addHook(array &$hooks, $name, callable $callback)
	{
		$hooks[$name][] = $callback;
	}

	static public function removeAction($name, callable $callback)
	{
		self::_removeHook(self::$_actions, $name, $callback);
	}

	static public function removeFilter($name, callable $callback)
	{
		self::_removeHook(self::$_filters, $name, $callback);
	}

	static private function _removeHook(array &$hooks, $name, callable $callback)
	{
		if (!isset($hooks[$name])) {
			throw HooksException::hookDoesNotExist($name);
		}

		foreach ($hooks[$name] as $key => $iteratedCallback) {
			if ($iteratedCallback === $callback) {
				unset($hooks[$name][$key]);
			}
		}
	}

	static public function runAction($name, ...$arguments)
	{
		self::_runHook(self::$_actions, $name, $arguments);
	}

	static public function applyFilter($name, ...$arguments)
	{
		if (!isset($arguments[0])) {
			throw HooksException::filterRequiresArgument();
		}

		return self::_runHook(self::$_filters, $name, $arguments, true);
	}

	static private function _runHook(array &$hooks, $name, array $arguments, $keepFirstArgument = false)
	{
		if (!isset($hooks[$name])) {
			return ($keepFirstArgument) ? $arguments[0] : null;
		}

		foreach ($hooks[$name] as $callback) {
			try {
				if ($keepFirstArgument) {
					$arguments[0] = call_user_func_array($callback, $arguments);
				}
				else {
					call_user_func_array($callback, $arguments);
				}
			}
			catch (\ErrorException $e) {
				// When you call function without required number of arguments, PHP emits E_WARNING error that will be converted
				// to \ErrorException. We would check whether number of given $arguments match to number of callback required arguments.
				$requiredArgsCount = (new \ReflectionFunction($callback))->getNumberOfRequiredParameters();
				$givenArgsCount = count($arguments);
				if ($requiredArgsCount > $givenArgsCount) {
					throw HooksException::hookWrongArgumentsCount($name, $requiredArgsCount, $givenArgsCount);
				}
				// When error is different, throw it again to default exception handler.
				else {
					throw $e;
				}
			}
		}

		return ($keepFirstArgument) ? $arguments[0] : null;
	}
}

class HooksException extends Exception
{
	static public function hookWrongArgumentsCount($hookName, $requiredArgsCount, $givenArgsCount)
	{
		return new self('Callback of ' . $hookName . ' hook expect ' . $requiredArgsCount . ' required arguments, ' . $givenArgsCount . ' given.', 1);
	}
	static public function hookDoesNotExist($hookName)
	{
		return new self('Hook named ' . $hookName . ' does not exists.', 2);
	}
	static public function filterRequiresArgument()
	{
		return new self('Filter always requires one argument at least.', 3);
	}
}