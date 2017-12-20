<?php

/**
* WizyTówka 5
* Generates HTML lists for admin panel with various elements (pages, files, users etc.).
*/
namespace WizyTowka;

class HTMLElementsList extends HTMLTag
{
	private $_collection;
	private $_emptyMessage;

	private $_callbackTitle;
	private $_callbackLink;
	private $_callbackMenu;
	private $_callbackRadio;

	private $_radioFieldName;
	private $_radioFieldCurrentValue;

	public function collection(array &$collection)
	{
		$this->_collection = &$collection;

		return $this;
	}

	public function title(callable $callback)
	{
		$this->_callbackTitle = $callback;

		return $this;
	}

	public function link(callable $callback)
	{
		if ($this->_callbackRadio) {
			throw HTMLElementsListException::radioOrLink();
		}
		$this->_callbackLink = $callback;

		return $this;
	}

	public function radio($name, callable $fieldValueCallback, $currentValue)
	{
		if ($this->_callbackLink) {
			throw HTMLElementsListException::radioOrLink();
		}
		$this->_radioFieldName = $name;
		$this->_radioFieldCurrentValue = $currentValue;
		$this->_callbackRadio = $fieldValueCallback;

		return $this;
	}

	public function option(...$arguments)
	{
		return $this->radio(...$arguments);
	}

	public function menu(callable $callback)
	{
		$this->_callbackMenu = $callback;

		return $this;
	}

	public function emptyMessage($text)
	{
		$this->_emptyMessage = (string)$text;

		return $this;
	}

	public function output()
	{
		if (is_null($this->_collection) or empty($this->_callbackTitle)) {
			throw HTMLElementsListException::missingInformation();
		}

		if ($this->_collection) {
			echo '<ul', $this->_CSSClass ? ' class="'.$this->_CSSClass.'">' : '>';

			foreach ($this->_collection as $element) {
				$title = call_user_func($this->_callbackTitle, $element);
				// Syntax like ($this->_callbackTitle)($element) cannot be used because of backwards compatibility with PHP 5.6.

				echo '<li>';

				echo '<span>';
				if ($this->_callbackLink) {
					echo '<a href="', call_user_func($this->_callbackLink, $element), '">', $title, '</a>';
				}
				elseif ($this->_callbackRadio) {
					isset($id) ? ++$id : $id=0;
					$fieldValue = call_user_func($this->_callbackRadio, $element);
					echo '<input type="radio" id="', $this->_radioFieldName, $id, '" name="', $this->_radioFieldName, '" value="', $fieldValue, '"',
						 ($this->_radioFieldCurrentValue == $fieldValue ? ' checked>' : '>'),
						 '<label for="', $this->_radioFieldName, $id, '">', $title, '</label>';
				}
				else {
					echo $title;
				}
				echo '</span>';

				if ($this->_callbackMenu) {
					echo '<ul>';
					foreach (call_user_func($this->_callbackMenu, $element) as $option) {
						// $option[0] — menu item label,
						// $option[1] — menu item URL address,
						// $option[2] — optional, menu item CSS class.
						echo (!empty($option[2]) ? '<li class="'.$option[2].'">' : '<li>'),
							 '<a href="', $option[1], '" aria-label="', $option[0], ' — ', $title, '">', $option[0], '</a>',
							 '</li>';
					}
					echo '</ul>';
				}

				echo '</li>';
			}

			echo '</ul>';
		}
		elseif ($this->_emptyMessage) {
			echo '<p class="', $this->_CSSClass,' emptyMessage">', $this->_emptyMessage, '</p>';
		}
	}
}

class HTMLElementsListException extends Exception
{
	static public function missingInformation()
	{
		return new self('You must provide collection and title callback at least.', 1);
	}
	static public function radioOrLink()
	{
		return new self('You must not specify radio field callback and link callback at the same time.', 2);
	}
}