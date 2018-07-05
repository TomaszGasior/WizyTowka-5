<?php

/**
* WizyTówka 5
* HTML <HEAD> tag renderer.
*/
namespace WizyTowka;

class HTMLHead extends HTMLTag
{
	private $_previousAssetsPath = '';
	private $_assetsPath = '';
	private $_assetsPathBase = '';

	private $_titlePattern = '';

	private $_tags = [];

	public function __debugInfo() : array
	{
		return $this->_tags;
	}

	public function base(string $href = null, array $HTMLAttributes = []) : self
	{
		$this->_removeTag(__FUNCTION__);

		if ($href) {
			$tagName = __FUNCTION__;
			$HTMLAttributes['href'] = $href;

			$this->_tags[] = compact('tagName', 'HTMLAttributes');
		}
		return $this;
	}

	public function title(string $title = null, array $HTMLAttributes = []) : self
	{
		$this->_removeTag(__FUNCTION__);

		if ($title) {
			if ($this->_titlePattern) {
				$title = sprintf($this->_titlePattern, $title);
			}

			$tagName = __FUNCTION__;
			$content = HTML::escape($title);

			$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		}
		return $this;
	}

	public function meta(string $name, string $content, array $HTMLAttributes = []) : self
	{
		$tagName = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['content'] = HTML::escape($content);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function httpEquiv(string $header, string $content, array $HTMLAttributes = []) : self
	{
		$tagName = 'meta';
		$HTMLAttributes['http-equiv'] = $header;
		$HTMLAttributes['content']    = HTML::escape($content);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function link(string $rel, string $href, array $HTMLAttributes = []) : self
	{
		$tagName = __FUNCTION__;
		$HTMLAttributes['rel']  = $rel;
		$HTMLAttributes['href'] = $this->_prepareAssetPath($href);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function script(string $src, array $HTMLAttributes = []) : self
	{
		$tagName = __FUNCTION__;
		$content = '';   // Render also ending </script> tag.
		$HTMLAttributes['src'] = $this->_prepareAssetPath($src);

		$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		return $this;
	}

	public function stylesheet(string $href, array $HTMLAttributes = []) : self
	{
		return $this->link(__FUNCTION__, $href, $HTMLAttributes);
	}

	public function inlineScript(string $code, array $HTMLAttributes = []) : self
	{
		$tagName = 'script';
		$content = $code;

		$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		return $this;
	}

	public function inlineStylesheet(string $stylesheet, array $HTMLAttributes = []) : self
	{
		$tagName = 'style';
		$content = $stylesheet;

		$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		return $this;
	}

	public function removeMeta(string $name, string $content = null) : self
	{
		if ($content) {
			$content = HTML::escape($content);
		}
		$this->_removeTag('meta', array_filter(['name' => $name, 'content' => $content]));

		return $this;
	}

	public function removeHttpEquiv(string $header, string $content = null) : self
	{
		if ($content) {
			$content = HTML::escape($content);
		}
		$this->_removeTag('meta', array_filter(['http-equiv' => $header, 'content' => $content]));

		return $this;
	}

	public function removeScript(string $src) : self
	{
		$this->_removeTag('script', ['src' => $src]);

		return $this;
	}

	public function removeLink(string $rel, string $href = null) : self
	{
		$this->_removeTag('link', array_filter(['rel' => $rel, 'href' => $href]));

		return $this;
	}

	public function removeStylesheet(string $href) : self
	{
		return $this->removeLink('stylesheet', $href);
	}

	public function getTitlePattern() : string
	{
		return $this->_titlePattern;
	}

	public function setTitlePattern(string $titlePattern) : bool
	{
		if (strpos($titlePattern, '%s') !== false) {
			$this->_titlePattern = $titlePattern;
			return true;
		}

		return false;
	}

	public function getAssetsPath() : string
	{
		return $this->_assetsPath;
	}

	public function setAssetsPath(string $assetsPath) : void
	{
		$this->_previousAssetsPath = $this->_assetsPath;
		$this->_assetsPath         = $assetsPath;
	}

	public function restoreAssetsPath() : bool
	{
		if ($this->_previousAssetsPath) {
			$this->_assetsPath = $this->_previousAssetsPath;
			return true;
		}

		return false;
	}

	public function getAssetsPathBase() : string
	{
		return $this->_assetsPathBase;
	}

	public function setAssetsPathBase(string $assetsPathBase) : void
	{
		$this->_assetsPathBase = $assetsPathBase;
	}

	private function _prepareAssetPath($file) : string
	{
		$fullAssetsPath = ($this->_assetsPathBase ? $this->_assetsPathBase.'/' : '') . $this->_assetsPath;
		return (($this->_assetsPath and !parse_url($file, PHP_URL_HOST)) ? $fullAssetsPath.'/' : '') . $file;
	}

	private function _removeTag(string $tagName, array $matchHTMLAttributes = []) : void
	{
		foreach ($this->_tags as $key => $tag) {
			if ($tag['tagName'] == $tagName
				and count(array_intersect_assoc($tag['HTMLAttributes'], $matchHTMLAttributes)) == count($matchHTMLAttributes)) {
				unset($this->_tags[$key]);
			}
		}
	}

	public function output() : void
	{
		foreach ($this->_tags as $tag) {
			$this->_renderHTMLOpenTag($tag['tagName'], $tag['HTMLAttributes']);
			if (isset($tag['content'])) {
				echo $tag['content'], '</', $tag['tagName'], '>';
			}
		}
	}
}