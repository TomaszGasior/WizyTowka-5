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

	private $_tags = [];

	public function __debugInfo()
	{
		return $this->_tags;
	}

	public function base($href = null, array $HTMLAttributes = [])
	{
		$this->_removeTag(__FUNCTION__);

		if ($href) {
			$tagName = __FUNCTION__;
			$HTMLAttributes['href'] = $href;

			$this->_tags[] = compact('tagName', 'HTMLAttributes');
		}
		return $this;
	}

	public function title($title = null, array $HTMLAttributes = [])
	{
		$this->_removeTag(__FUNCTION__);

		if ($title) {
			$tagName = __FUNCTION__;
			$content = HTML::escape($title);

			$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		}
		return $this;
	}

	public function meta($name, $content, array $HTMLAttributes = [])
	{
		$tagName = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['content'] = HTML::escape($content);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function httpEquiv($header, $content, array $HTMLAttributes = [])
	{
		$tagName = 'meta';
		$HTMLAttributes['http-equiv'] = $header;
		$HTMLAttributes['content']    = HTML::escape($content);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function link($rel, $href, array $HTMLAttributes = [])
	{
		$tagName = __FUNCTION__;
		$HTMLAttributes['rel']  = $rel;
		$HTMLAttributes['href'] = $this->_prepareAssetPath($href);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function script($src, array $HTMLAttributes = [])
	{
		$tagName = __FUNCTION__;
		$content = '';   // Render also ending </script> tag.
		$HTMLAttributes['src'] = $this->_prepareAssetPath($src);

		$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		return $this;
	}

	public function stylesheet($href, array $HTMLAttributes = [])
	{
		return $this->link(__FUNCTION__, $href, $HTMLAttributes);
	}

	public function inlineScript($code, array $HTMLAttributes = [])
	{
		$tagName = 'script';
		$content = $code;

		$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		return $this;
	}

	public function inlineStylesheet($stylesheet, array $HTMLAttributes = [])
	{
		$tagName = 'style';
		$content = $stylesheet;

		$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		return $this;
	}

	public function removeMeta($name, $content = null)
	{
		$this->_removeTag('meta', array_filter(['name' => $name, 'content' => $content]));

		return $this;
	}

	public function removeHttpEquiv($header, $content = null)
	{
		$this->_removeTag('meta', array_filter(['http-equiv' => $header, 'content' => $content]));

		return $this;
	}

	public function removeScript($src)
	{
		$this->_removeTag('script', ['src' => $src]);

		return $this;
	}

	public function removeLink($rel, $href = null)
	{
		$this->_removeTag('link', array_filter(['rel' => $rel, 'href' => $href]));

		return $this;
	}

	public function removeStylesheet($href)
	{
		return $this->removeLink('stylesheet', $href);
	}

	public function getAssetsPath()
	{
		return $this->_assetsPath;
	}

	public function setAssetsPath($assetsPath)
	{
		$this->_previousAssetsPath = $this->_assetsPath;
		$this->_assetsPath         = (string)$assetsPath;
	}

	public function restoreAssetsPath()
	{
		if ($this->_previousAssetsPath) {
			$this->_assetsPath = $this->_previousAssetsPath;
			return true;
		}

		return false;
	}

	public function getAssetsPathBase()
	{
		return $this->_assetsPathBase;
	}

	public function setAssetsPathBase($assetsPathBase)
	{
		$this->_assetsPathBase = (string)$assetsPathBase;
	}

	private function _prepareAssetPath($file)
	{
		$fullAssetsPath = ($this->_assetsPathBase ? $this->_assetsPathBase.'/' : '') . $this->_assetsPath;
		return (($this->_assetsPath and !parse_url($file, PHP_URL_HOST)) ? $fullAssetsPath.'/' : '') . $file;
	}

	private function _removeTag($tagName, array $matchHTMLAttributes = [])
	{
		foreach ($this->_tags as $key => $tag) {
			if ($tag['tagName'] == $tagName
				and count(array_intersect_assoc($tag['HTMLAttributes'], $matchHTMLAttributes)) == count($matchHTMLAttributes)) {
				unset($this->_tags[$key]);
			}
		}
	}

	public function output()
	{
		foreach ($this->_tags as $tag) {
			$this->_renderHTMLOpenTag($tag['tagName'], $tag['HTMLAttributes']);
			if (isset($tag['content'])) {
				echo $tag['content'], '</', $tag['tagName'], '>';
			}
		}
	}
}