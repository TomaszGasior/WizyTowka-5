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
			throw new Exception('Hook named ' . $name . ' does not exists.', 20);
		}

		foreach ($hooks[$name] as $key => $iteratedCallback) {
			if ($iteratedCallback === $callback) {
				unset($hooks[$name][$key]);
			}
		}
	}

	static public function runAction($name)
	{
		$arguments = array_slice(func_get_args(), 1);

		self::_runHook(self::$_actions, $name, $arguments);
	}

	static public function applyFilter($name)
	{
		$arguments = array_slice(func_get_args(), 1);

		if (!isset($arguments[0])) {
			throw new Exception('Each filter must use one argument at least.', 6);
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
				// When you call function without required number of arguments, PHP emits E_WARNING error that will be converted to \ErrorException by our ErrorHandler.
				// We would check whether number of given $arguments match to number of callback required arguments.
				$requiredArgsCount = (new \ReflectionFunction($callback))->getNumberOfRequiredParameters();
				$givenArgsCount = count($arguments);
				if ($requiredArgsCount > $givenArgsCount) {
					throw new Exception('Callback of ' . $name . ' hook expect ' . $requiredArgsCount . ' required arguments, ' . $givenArgsCount . ' given.', 5);
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