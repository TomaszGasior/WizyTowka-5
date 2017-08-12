<?php

/**
* WizyTówka 5
* HTML <HEAD> tag renderer.
*/
namespace WizyTowka;

class HTMLHead
{
	private $_assetsPath;

	private $_base;
	private $_title = 'Untitled HTML document';
	private $_metaTags = [];
	private $_httpEquiv = [];
	private $_styles = [];
	private $_stylesInline = [];
	private $_scripts = [];
	private $_scriptsInline = [];

	public function setAssetsPath($assetsPath)
	{
		$this->_assetsPath = $assetsPath;

		return $this;
	}

	public function setBase($base)
	{
		$this->_base = $base;

		return $this;
	}

	public function setTitle($title)
	{
		$this->_title = htmlspecialchars($title);

		return $this;
	}

	public function setMeta($name, $value)
	{
		$this->_metaTags[$name] = htmlspecialchars($value);

		return $this;
	}

	public function setHttpEquiv($name, $value)
	{
		$this->_httpEquiv[$name] = htmlspecialchars($value);

		return $this;
	}

	public function addStyle($stylePath, $media = null)
	{
		$this->_styles[] = [
			($this->_assetsPath) ? $this->_assetsPath.'/'.$stylePath : $stylePath,
			$media
		];

		return $this;
	}

	public function removeStyle($styleFileName)
	{
		foreach ($this->_styles as $key => $style) {
			if (basename($style[0]) == $styleFileName) {
				unset($this->_styles[$key]);
			}
		}

		return $this;
	}

	public function addScript($scriptPath, $asyncInsteadDefer = false)
	{
		$this->_scripts[] = [
			($this->_assetsPath) ? $this->_assetsPath.'/'.$scriptPath : $scriptPath,
			$asyncInsteadDefer
		];

		return $this;
	}

	public function removeScript($scriptFileName)
	{
		foreach ($this->_scripts as $key => $script) {
			if (basename($script[0]) == $scriptFileName) {
				unset($this->_scripts[$key]);
			}
		}

		return $this;
	}

	public function addInlineStyle($styleCode)
	{
		$this->_stylesInline[] = $styleCode;

		return $this;
	}

	public function addInlineScript($scriptCode)
	{
		$this->_scriptsInline[] = $scriptCode;

		return $this;
	}

	public function __toString()
	{
		ob_start();

		// <base href="...">
		if ($this->_base) {
			echo '<base href="', $this->_base, '">';
		}

		// <title>...</title>
		echo '<title>', $this->_title, '</title>';

		// <meta http-equiv="..." content="...">
		foreach ($this->_httpEquiv as $header => $content) {
			if ($content) {
				echo '<meta http-equiv="', $header, '" content="', $content, '">';
			}
		}

		// <meta name="..." content="...">
		foreach ($this->_metaTags as $name => $content) {
			if ($content) {
				echo '<meta name="', $name, '" content="', $content, '">';
			}
		}

		// <link rel="stylesheet" href="..." media="...">
		foreach ($this->_styles as $style) {
			echo '<link rel="stylesheet" href="', $style[0], ($style[1] ? '" media="'.$style[1].'">' : '">');
		}

		// <script src="..." defer|async></script>
		foreach ($this->_scripts as $script) {
			echo '<script src="', $script[0], ($script[1] ? '" async></script>' : '" defer></script>');
		}

		// <style>...</style>
		foreach ($this->_stylesInline as $code) {
			echo '<style>', $code, '</style>';
		}

		// <script>...</script>
		foreach ($this->_scriptsInline as $code) {
			echo '<script>', $code, '</script>';
		}

		return ob_get_clean();
	}
}