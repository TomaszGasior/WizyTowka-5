<?php

/**
* WizyTówka 5
* This class stores string and manipulates them.
*/
namespace WizyTowka;

class Text
{
	private $_string;

	public function __construct($string)
	{
		$this->_string = (string)$string;
	}

	public function __debugInfo()
	{
		return [$this->_string];
	}

	public function __toString()
	{
		return $this->_string;
	}

	public function get()
	{
		return $this->_string;
	}

	public function getChar($position)
	{
		if ($position >= 0) {
			return mb_substr($this->_string, $position, 1);
		}
	}

	public function getLength()
	{
		return mb_strlen($this->_string);
	}

	public function lowercase()
	{
		$this->_string = mb_strtolower($this->_string);

		return $this;
	}

	public function uppercase()
	{
		$this->_string = mb_strtoupper($this->_string);

		return $this;
	}

	public function cut($length)
	{
		if ($length < 0) {
			$this->_string = mb_substr($this->_string, $length);
		}
		elseif ($length > 0) {
			$this->_string = mb_substr($this->_string, 0, $length);
		}

		return $this;
	}

	public function makeFragment($maxLength, $dots = '…')
	{
		if ($maxLength > 0 and $maxLength < $this->getLength()) {
			$removeBrokenWord = ($this->getChar($maxLength) != ' ');
			$this->cut($maxLength);

			if ($removeBrokenWord and $lastSpace = mb_strrpos($this->_string, ' ')) {
				$this->cut($lastSpace);
			}

			$this->_string .= $dots;
		}

		return $this;
	}

	public function makeMiddleFragment($maxLength, $dots = ' … ')
	{
		if ($maxLength > 0) {
			if ($maxLength % 2 != 0) {
				(integer)$maxLength--;
			}
			$lengthHalf = $maxLength / 2;

			$endFragment = mb_substr($this->_string, $lengthHalf*-1);
			if (($lastSpace = mb_strpos($endFragment, ' ')) !== false) {
				$endFragment = mb_substr($endFragment, $lastSpace+1);
			}

			$this->makeFragment($lengthHalf, $dots);
			$this->_string .= $endFragment;
		}

		return $this;
	}

	public function makeSlug()
	{
		$charsFrom = [' ', 'ą', 'ć', 'ę', 'ł', 'ó', 'ń', 'ś', 'ż', 'ź'];
		$charsTo   = ['-', 'a', 'c', 'e', 'l', 'o', 'n', 's', 'z', 'z'];

		$this->lowercase();
		$this->_string = str_replace($charsFrom, $charsTo, $this->_string);
		$this->_string = preg_replace(['/[^a-z0-9\-_]/', '/\-{2,}/'], ['', '-'], $this->_string);

		$this->_string = Hooks::applyFilter('textSlug', $this->_string);

		return $this;
	}

	public function formatAsDate($format = '%Y-%m-%d %H:%M:%S')
	{
		$this->_string = strftime(
			$format,
			(ctype_digit($this->_string)) ? $this->_string : strtotime($this->_string)
			// Notice: ctype_digit() works properly only when given argument is in string type!
			// More informations: http://php.net/manual/en/function.ctype-digit.php#refsect1-function.ctype-digit-notes
		);

		$this->_string = Hooks::applyFilter('textDateTime', $this->_string);

		return $this;
	}
}