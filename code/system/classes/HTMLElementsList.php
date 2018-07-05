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
	private $_callbackRadio;
	private $_callbackMenu;

	private $_radioFieldName;
	private $_radioFieldCurrentValue;

	private $_HTMLAttributesLink = [];
	private $_HTMLAttributesRadio = [];

	public function collection(array &$collection) : self
	{
		$this->_collection = &$collection;

		return $this;
	}

	public function title(callable $callback) : self
	{
		$this->_callbackTitle = $callback;

		return $this;
	}

	public function link(callable $callback, array $HTMLAttributes = []) : self
	{
		if ($this->_callbackRadio) {
			throw HTMLElementsListException::radioOrLink();
		}

		$this->_callbackLink       = $callback;
		$this->_HTMLAttributesLink = $HTMLAttributes;

		return $this;
	}

	public function radio(string $name, callable $fieldValueCallback, $currentValue, array $HTMLAttributes = []) : self
	{
		if ($this->_callbackLink) {
			throw HTMLElementsListException::radioOrLink();
		}

		$this->_radioFieldName         = $name;
		$this->_radioFieldCurrentValue = $currentValue;
		$this->_callbackRadio          = $fieldValueCallback;
		$this->_HTMLAttributesRadio    = $HTMLAttributes;

		return $this;
	}

	public function option(...$arguments) : self
	{
		return $this->radio(...$arguments);
	}

	public function menu(callable $callback) : self
	{
		$this->_callbackMenu = $callback;

		return $this;
	}

	public function emptyMessage(string $text) : self
	{
		$this->_emptyMessage = $text;

		return $this;
	}

	public function output() : void
	{
		if (is_null($this->_collection) or empty($this->_callbackTitle)) {
			throw HTMLElementsListException::missingInformation();
		}

		if ($this->_collection) {
			echo '<ul', $this->_CSSClass ? ' class="' . $this->_CSSClass . '">' : '>';

			foreach ($this->_collection as $element) {
				$title = ($this->_callbackTitle)($element);

				echo '<li>';

				echo '<span>';
				if ($this->_callbackLink) {
					$HTMLAttributes = [
						'href' => ($this->_callbackLink)($element)
					] + $this->_HTMLAttributesLink;

					$this->_renderHTMLOpenTag('a', $HTMLAttributes);
					echo $title, '</a>';
				}
				elseif ($this->_callbackRadio) {
					isset($id) ? ++$id : $id=0;
					$fieldValue = ($this->_callbackRadio)($element);

					$HTMLAttributes = [
						'type'    => 'radio',
						'id'      => $this->_radioFieldName . $id,
						'name'    => $this->_radioFieldName,
						'value'   => $fieldValue,
						'checked' => ($this->_radioFieldCurrentValue == $fieldValue)
					] + $this->_HTMLAttributesRadio;

					$this->_renderHTMLOpenTag('input', $HTMLAttributes);
					echo  '<label for="', $this->_radioFieldName, $id, '">', $title, '</label>';
				}
				else {
					echo $title;
				}
				echo '</span>';

				if ($this->_callbackMenu) {
					$menu = new HTMLMenu;
					foreach (($this->_callbackMenu)($element) as $item) {
						// $item[0] — menu item label,
						// $item[1] — menu item URL address,
						// $item[2] — optional, menu item CSS class.
						$menu->append(
							$item[0], $item[1], ($item[2] ?? ''),
							['aria-label' => $item[0] . ' — ' . strip_tags($title)]
						);
					}
					echo $menu;
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