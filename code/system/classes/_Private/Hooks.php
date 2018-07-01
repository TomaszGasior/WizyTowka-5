<?php

/**
* WizyTówka 5
* Manager of actions and filters. Concept inspired by WordPress hooks.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class Hooks
{
	private $_actions = [];
	private $_filters = [];

	public function addAction($name, callable $callback)
	{
		$this->_addHook($this->_actions, $name, $callback);
	}

	public function addFilter($name, callable $callback)
	{
		$this->_addHook($this->_filters, $name, $callback);
	}

	private function _addHook(array &$hooks, $name, callable $callback)
	{
		$hooks[$name][] = $callback;
	}

	public function removeAction($name, callable $callback)
	{
		$this->_removeHook($this->_actions, $name, $callback);
	}

	public function removeFilter($name, callable $callback)
	{
		$this->_removeHook($this->_filters, $name, $callback);
	}

	private function _removeHook(array &$hooks, $name, callable $callback)
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

	public function runAction($name, ...$arguments)
	{
		$this->_runHook($this->_actions, $name, $arguments);
	}

	public function applyFilter($name, ...$arguments)
	{
		if (!isset($arguments[0])) {
			throw HooksException::filterRequiresArgument($name);
		}

		return $this->_runHook($this->_filters, $name, $arguments, true);
	}

	private function _runHook(array &$hooks, $name, array $arguments, $keepFirstArgument = false)
	{
		if (!isset($hooks[$name])) {
			return ($keepFirstArgument) ? $arguments[0] : null;
		}

		foreach ($hooks[$name] as $callback) {
			try {
				if ($keepFirstArgument) {
					$arguments[0] = $callback(...$arguments);
				}
				else {
					$callback(...$arguments);
				}
			}
			// If hook callback is called without required number of arguments, hooks exception is thrown for more accurate information.
			catch (\ArgumentCountError $e) { // PHP 7.1.
				throw HooksException::hookWrongArgumentsCount(
					$name, (new \ReflectionFunction($callback))->getNumberOfRequiredParameters(), count($arguments)
				);
			}
			catch (\ErrorException $e) {  // PHP 7 and PHP 5.6.
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

class HooksException extends __\Exception
{
	static public function hookWrongArgumentsCount($hookName, $requiredArgsCount, $givenArgsCount)
	{
		return new self('Callback of ' . $hookName . ' hook expect ' . $requiredArgsCount . ' required arguments, ' . $givenArgsCount . ' given.', 1);
	}
	static public function hookDoesNotExist($hookName)
	{
		return new self('Hook named ' . $hookName . ' does not exists.', 2);
	}
	static public function filterRequiresArgument($hookName)
	{
		return new self('Filter ' . $hookName . ' always requires one argument at least.', 3);
	}
}