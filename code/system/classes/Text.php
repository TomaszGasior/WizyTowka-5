<?php

/**
* WizyTówka 5
* This class stores string and gives ability to manipulate it conveniently.
*/
namespace WizyTowka;

class Text implements \ArrayAccess, \IteratorAggregate
{
	const TYPOGRAPHY_OTHER   = 0b00000001;
	const TYPOGRAPHY_QUOTES  = 0b00000010;
	const TYPOGRAPHY_ORPHANS = 0b00000100;
	const TYPOGRAPHY_DASHES  = 0b00001000;

	private $_string;

	public function __construct($string)
	{
		$this->_string = (string)$string;    // See comment in formatAsDateTime().
	}

	public function __debugInfo()
	{
		return [$this->_string];
	}

	public function __toString()
	{
		return $this->_string;
	}

	public function offsetExists ($offset)   // ArrayAccess interface.
	{
		return ($this->getChar($offset) !== null);
	}

	public function offsetGet ($offset)   // ArrayAccess interface.
	{
		if (($char = $this->getChar($offset)) === null) {
			trigger_error('Uninitialized string offset: '.$offset, E_USER_NOTICE);
		}

		return $char;
	}

	public function offsetSet ($offset, $char)   // ArrayAccess interface.
	{
		if ($this->getChar($offset) === null) {
			trigger_error('Illegal string offset: '.$offset, E_USER_NOTICE);
			return;
		}

		$char = mb_substr((string)$char, 0, 1);
		if ($char === '') {
			trigger_error('Cannot assign an empty string to a string offset', E_USER_NOTICE);
			return;
		}

		if ($offset < 0) {
			$offset += $this->getLength();
		}

		$before = mb_substr($this->_string, 0, $offset);
		$after  = mb_substr($this->_string, $offset+1);

		$this->_string = $before . $char . $after;
	}

	public function offsetUnset ($offset)   // ArrayAccess interface.
	{
		trigger_error('Cannot unset string offsets', E_USER_NOTICE);    // Match PHP native behavior.
	}

	public function getIterator()   // IteratorAggregate interface.
	{
		for ($offset = 0; $offset < $this->getLength(); $offset++) {
			yield $this[$offset];
		}
	}

	public function get()
	{
		return $this->_string;
	}

	public function getChar($position)
	{
		if (is_integer($position)) {
			$testNumber = ($position < 0) ? (abs($position) - 1) : $position;

			if ($testNumber < $this->getLength()) {
				return mb_substr($this->_string, $position, 1);
			}
		}

		return null;
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

	public function cut($from, $length = null)
	{
		if (is_integer($from)) {
			$this->_string = is_integer($length) ? mb_substr($this->_string, $from, $length) : mb_substr($this->_string, $from);
		}

		return $this;
	}

	public function replace(array $replacements, $caseInsensitive = false)
	{
		if ($caseInsensitive) {
			foreach ($replacements as $from => $to) {
				$this->_string = mb_ereg_replace($from, $to, $this->_string, 'i');
			}
		}
		else {
			$this->_string = str_replace(array_keys($replacements), $replacements, $this->_string);
		}

		return $this;
	}

	public function correctTypography($flags)
	{
		// Do not correct typography in contents of <code> and <pre> HTML tags.
		$partsPreCode = preg_split('/(<\/{0,1}(?:pre|code)(?: [^<>]*|)>)/', $this->_string, -1,  PREG_SPLIT_DELIM_CAPTURE);

		foreach ($partsPreCode as $key => &$stringPart) {
			if ($key % 4 != 0) {
				// Typography corrections should be applied only to $partsPreCode[0], $partsPreCode[4], $partsPreCode[8],
				// $partsPreCode[12], etc. Other $partsPreCode elements contain <pre> or <code> HTML tags and tags contents.
				continue;
			}

			// Polish one-letter words at the end of lines. More here: https://pl.wikipedia.org/wiki/Sierotka_(typografia)
			if ($flags & self::TYPOGRAPHY_ORPHANS) {
				$stringPart = preg_replace(
					'/(( |\()(o|u|w|z|i|a)) /i',
					'$1'.(PHP_VERSION_ID < 70000 ? json_decode('"\u00A0"') : "\u{00A0}"), // "No break space" character.
					// PHP 5.6 backwards compatibility.
					// More here: http://php.net/manual/en/migration70.new-features.php#migration70.new-features.unicode-codepoint-escape-syntax
					$stringPart
				);
			}

			// Em-dash character. More here: https://pl.wikipedia.org/wiki/Pauza_(znak_typograficzny)
			if ($flags & self::TYPOGRAPHY_DASHES) {
				$stringPart = str_replace(
					[' - ', ' -<', '>- '],
					[' — ', ' —<', '>— '],
					$stringPart
				);
			}

			// Ellipsis character. More here: https://pl.wikipedia.org/wiki/Wielokropek
			if ($flags & self::TYPOGRAPHY_OTHER) {
				$stringPart = str_replace(
					['... ', ' ...', "...\n", "\n...", '>...', '...<'],
					['… ',   ' …',   "…\n",   "\n…",   '>…',   '…<'  ],
					$stringPart
				);
			}

			if ($flags & self::TYPOGRAPHY_OTHER or $flags & self::TYPOGRAPHY_QUOTES) {
				// Do not correct apostrophes and quotation marks in HTML open tags.
				$partsHTMLOpenTags = preg_split('/(<[^\/]* [^<>]*>)/', $stringPart, -1,  PREG_SPLIT_DELIM_CAPTURE);

				foreach ($partsHTMLOpenTags as $key => &$nestedStringPart) {
					if ($key % 2 != 0) {
						// Typography corrections should be applied only to $partsHTMLOpenTags[0], $partsHTMLOpenTags[2], $partsHTMLOpenTags[4], $partsHTMLOpenTags[6], etc. Other $partsHTMLOpenTags elements contain HTML open tags.
						continue;
					}

					// Proper Polish apostrophe character. More here: https://pl.wikipedia.org/wiki/Apostrof
					if ($flags & self::TYPOGRAPHY_OTHER) {
						$nestedStringPart = str_replace('\'', '’', $nestedStringPart);
					}

					// Proper Polish quotation marks. More here: https://pl.wikipedia.org/wiki/Cudzysłów
					if ($flags & self::TYPOGRAPHY_QUOTES) {
						$nestedStringPart = preg_replace('/"([^"]*)"/', '„$1”', $nestedStringPart);
					}
				}
				unset($nestedStringPart);

				$stringPart = join($partsHTMLOpenTags);
			}
		}

		$this->_string = join($partsPreCode);

		return $this;
	}

	public function makeFragment($maxLength, $dots = '…')
	{
		if ($maxLength > 0 and $maxLength < $this->getLength()) {
			$removeBrokenWord = ($this->getChar($maxLength) != ' ');
			$this->cut(0, $maxLength);

			if ($removeBrokenWord and $lastSpace = mb_strrpos($this->_string, ' ')) {
				$this->cut(0, $lastSpace);
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

	public function makeSlug($lowercase = true)
	{
		$charsFrom = [' ', 'ą', 'ć', 'ę', 'ł', 'ó', 'ń', 'ś', 'ż', 'ź'];
		$charsTo   = ['-', 'a', 'c', 'e', 'l', 'o', 'n', 's', 'z', 'z'];

		if ($lowercase) {
			$this->lowercase();
		}

		$this->_string = str_replace($charsFrom, $charsTo, $this->_string);
		$this->_string = preg_replace(['/[^a-z0-9\-_\.]/', '/\-{2,}/'], ['', '-'], $this->_string);

		return $this;
	}

	public function formatAsDateTime($format = '%Y-%m-%d %H:%M:%S')
	{
		if ($format) {
			$isWindowsOS = !strncasecmp(PHP_OS, 'win', 3);

			// Windows does not support "%e" and "%k" modifiers.
			// More here: http://php.net/manual/en/function.strftime.php#example-2583
			if ($isWindowsOS) {
			    $format = preg_replace(['#(?<!%)((?:%%)*)%e#', '#(?<!%)((?:%%)*)%k#'], ['\1%#d', '\1%#H'], $format);
			}

			$dateTimeText = strftime(
				$format,
				ctype_digit($this->_string) ? $this->_string : strtotime($this->_string)
				// Notice: ctype_digit() works properly only when given argument is in string type!
				// More here: http://php.net/manual/en/function.ctype-digit.php#refsect1-function.ctype-digit-notes
			);

			// Windows uses wrong encoding in strftime().
			if ($isWindowsOS) {
				$dateTimeText = mb_convert_encoding($dateTimeText, mb_internal_encoding(), 'ISO-8859-2');
			}

			// Some operating systems have bug in Polish locale causing month names in wrong form.
			if (strpos($format, ' %B') !== false and (strftime('%B', 1) == 'styczeń' or $isWindowsOS)) {
				$monthNames = [
					'styczeń'  => 'stycznia', 'luty'        => 'lutego',       'marzec'   => 'marca',     'kwiecień' => 'kwietnia',
					'maj'      => 'maja',     'czerwiec'    => 'czerwca',      'lipiec'   => 'lipca',     'sierpień' => 'sierpnia',
					'wrzesień' => 'września', 'październik' => 'października', 'listopad' => 'listopada', 'grudzień' => 'grudnia'
				];
				$dateTimeText = str_replace(array_keys($monthNames), $monthNames, $dateTimeText);
			}

			$this->_string = $dateTimeText;
		}

		return $this;
	}

	public function formatAsFileSize($binaryUnits = true)
	{
		if (ctype_digit($this->_string)) {
			$nbsp = PHP_VERSION_ID < 70000 ? json_decode('"\u00A0"') : "\u{00A0}";   // PHP 5.6 backwards compatibility.

			$units = $binaryUnits ? [
				'GiB' => 1073741824,
				'MiB' => 1048576,
				'KiB' => 1024,
			] : [
				'GB' => 1000000000,
				'MB' => 1000000,
				'kB' => 1000,
			];

			foreach ($units as $unitName => $unitFactor) {
				if ($this->_string >= $unitFactor) {
					$fileSizeText = round($this->_string / $unitFactor, 1) . $nbsp . $unitName;
					break;
				}
			}

			if (empty($fileSizeText)) {
				$fileSizeText = $this->_string . $nbsp . 'B';
			}

			$this->_string = $fileSizeText;
		}

		return $this;
	}
}