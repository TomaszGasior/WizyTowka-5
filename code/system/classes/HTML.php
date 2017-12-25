<?php

/**
* WizyTówka 5
* HTML formatting utilities.
*/
namespace WizyTowka;

trait HTML
{
	static public function correctTypography($text)
	{
		$settings = Settings::get();

		$flags = ($settings->typographyOther   ? Text::TYPOGRAPHY_OTHER   : 0) |
		         ($settings->typographyDashes  ? Text::TYPOGRAPHY_DASHES  : 0) |
		         ($settings->typographyQuotes  ? Text::TYPOGRAPHY_QUOTES  : 0) |
		         ($settings->typographyOrphans ? Text::TYPOGRAPHY_ORPHANS : 0);

		return $flags ? (new Text($text))->correctTypography($flags) : $text;
	}

	static private function _prepateTimeTag($timestamp, $visibleFormat, $HTMLFormat)
	{
		$value     = (new Text($timestamp))->formatAsDateTime($visibleFormat);
		$HTMLValue = (new Text($timestamp))->formatAsDateTime($HTMLFormat);

		return '<time datetime="' . $HTMLValue . '">' . $value . '</time>';
	}

	static public function formatDateTime($timestamp)
	{
		$settings = Settings::get();

		$format = [$settings->dateDateFormat, $settings->dateSeparator, $settings->dateTimeFormat];
		if ($settings->dateSwapTime) {
			$format = array_reverse($format);
		}

		return self::_prepateTimeTag($timestamp, join($format), '%FT%T%z');
	}

	static public function formatDate($timestamp)
	{
		return self::_prepateTimeTag($timestamp, Settings::get('dateDateFormat'), '%F');
	}

	static public function formatTime($timestamp)
	{
		return self::_prepateTimeTag($timestamp, Settings::get('dateTimeFormat'), '%T%z');
	}
}