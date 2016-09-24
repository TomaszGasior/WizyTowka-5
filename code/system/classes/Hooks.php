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

	static public function addAction($name, callable $callback, $position = null)
	{
		self::_addHook(self::$_actions, $name, $callback, $position);
	}

	static public function addFilter($name, callable $callback, $position = null)
	{
		self::_addHook(self::$_filters, $name, $callback, $position);
	}

	static private function _addHook(array &$hooks, $name, callable $callback, $position = null)
	{
		if (is_integer($position)) {
			if (isset($hooks[$name][$position])) {
				throw new \Exception('Callback cannot be added to ' . $name . ' hook in position ' . $position . '.');
			}
			else {
				$hooks[$name][$position] = $callback;
				ksort($hooks[$name]);
			}
		}
		else {
			$hooks[$name][] = $callback;
		}
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
			return;
		}

		foreach ($hooks[$name] as $position => $iteratedCallback) {
			if ($iteratedCallback === $callback) {
				unset($hooks[$name][$position]);
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
			throw new \Exception('Each filter must use one argument at least.');
			return;
		}

		return self::_runHook(self::$_filters, $name, $arguments, true);
	}

	static private function _runHook(array &$hooks, $name, array $arguments, $keepAndReturnFirstArgument = false)
	{
		if (!isset($hooks[$name])) {
			return ($keepAndReturnFirstArgument) ? $arguments[0] : null;
		}

		foreach ($hooks[$name] as $position => $callback) {
			try {
				if ($keepAndReturnFirstArgument) {
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
					throw new \Exception('Callback of ' . $name . ' hook in position ' . $position . ' expect ' . $requiredArgsCount . ' required arguments, ' . $givenArgsCount . ' given.');
				}
				// When error is different, throw it again to default exception handler.
				else
					throw $e;
			}
		}

		return ($keepAndReturnFirstArgument) ? $arguments[0] : null;
	}
}