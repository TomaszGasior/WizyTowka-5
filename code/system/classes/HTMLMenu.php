<?php

/**
* WizyTówka 5
* HTML navigation menu generator with unordered list <ul>.
*/
namespace WizyTowka;

class HTMLMenu extends HTMLTag implements \IteratorAggregate, \Countable
{
	private $_renderingInProgress = false;

	private $_items = [];

	public function __debugInfo() : array
	{
		sort($this->_items);

		return $this->_items;
	}

	public function getIterator() : iterable  // For IteratorAggregate interface.
	{
		sort($this->_items);

		foreach ($this->_items as $item) {
			unset($item['_auto']);   // "_auto" should be considered as private.
			yield (object)$item;
		}
	}

	public function count() : int  // For Countable interface.
	{
		return count($this->_items);
	}

	public function prepend(...$arguments) : self
	{
		$position = $this->_items ? (min($this->_items)['position'] - 1) : 1;
		$this->_addItem(true, $position, ...$arguments);

		return $this;
	}

	public function append(...$arguments) : self
	{
		$position = $this->_items ? (max($this->_items)['position'] + 1) : 1;
		$this->_addItem(true, $position, ...$arguments);

		return $this;
	}

	public function insert(...$arguments) : self
	{
		$this->_addItem(false, ...$arguments);

		return $this;
	}

	public function _addItem(bool $_auto, $position, string $label, $content, ?string $CSSClass = null, array $HTMLAttributes = [], bool $visible = true) : void
	{
		if (!is_string($content) and (!is_object($content) or !($content instanceof $this))) {
			throw HTMLMenuException::invalidContentValue();
		}
		if (!is_int($position) and !is_float($position)) {
			throw HTMLMenuException::invalidPositionValue();
		}

		// "position" and "_auto" must be at the beginning.
		$this->_items[] = compact('position', '_auto', 'label', 'content', 'CSSClass', 'HTMLAttributes', 'visible');
	}

	public function replace($position, ...$arguments) : self
	{
		$this->removeByPosition($position);
		$this->insert($position, ...$arguments);

		return $this;
	}

	public function removeByContent($content) : self
	{
		$this->_removeItem('content', $content);

		return $this;
	}

	public function removeByLabel(string $label) : self
	{
		$this->_removeItem('label', $label);

		return $this;
	}

	public function removeByPosition($position) : self
	{
		if (!is_int($position) and !is_float($position)) {
			throw HTMLMenuException::invalidPositionValue();
		}
		$this->_removeItem('position', $position);

		return $this;
	}

	private function _removeItem(string $key, $value) : void
	{
		$this->_items = array_filter($this->_items, function($item) use ($key, $value){
			return $item[$key] != $value;
		});
	}

	public function output() : void
	{
		if ($this->_renderingInProgress) {
			throw HTMLMenuException::renderingInProgress();
		}
		$this->_renderingInProgress = true;

		// Array will be sorted by numeric value of "position" element of each nested array.
		// Second element of nested arrays called "_auto" is used to keep menu items added by insert() (with false
		// as "_auto" value) before items added by prepend() or append() (with true as "_auto" value).
		sort($this->_items);

		if ($this->_items) {
			echo '<ul', $this->_CSSClass ? ' class="'.$this->_CSSClass.'">' : '>';

			foreach ($this->_items as $element) {
				if (!$element['visible']) {
					continue;
				}

				echo '<li', $element['CSSClass'] ? ' class="' . $element['CSSClass'] . '">' : '>';

				if (is_object($element['content'])) {
					echo '<span>', $element['label'], '</span>', (string)$element['content'];
				}
				else {
					$element['HTMLAttributes']['href'] = $element['content'];

					$this->_renderHTMLOpenTag('a', $element['HTMLAttributes']);
					echo $element['label'], '</a>';
				}

				echo '</li>';
			}

			echo '</ul>';
		}

		$this->_renderingInProgress = false;
	}
}

class HTMLMenuException extends Exception
{
	static public function renderingInProgress()
	{
		return new self('Menu contains itself as item, infinite recursion.', 1);
	}
	static public function invalidContentValue()
	{
		return new self('You must pass string with URL address or other instance of menu class as content of menu item.', 2);
	}
	static public function invalidPositionValue()
	{
		return new self('Position of menu item must be passed as integer or float.', 3);
	}
}