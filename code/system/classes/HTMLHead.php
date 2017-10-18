<?php

/**
* WizyTówka 5
* HTML <HEAD> tag renderer.
*/
namespace WizyTowka;

class HTMLHead extends HTMLTag
{
	private $_assetsPath = '';
	private $_tags       = [];

	public function base($href = null, array $HTMLAttributes = [])
	{
		if ($href) {
			$tagName = __FUNCTION__;
			$HTMLAttributes['href'] = $href;

			$this->_tags[] = compact('tagName', 'HTMLAttributes');
		}
		else {
			$this->_removeTag('base');
		}

		return $this;
	}

	public function title($title = null, array $HTMLAttributes = [])
	{
		if ($title) {
			$tagName = __FUNCTION__;
			$content = htmlspecialchars($title);

			$this->_tags[] = compact('tagName', 'content', 'HTMLAttributes');
		}
		else {
			$this->_removeTag('title');
		}

		return $this;
	}

	public function meta($name, $content, array $HTMLAttributes = [])
	{
		$tagName = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['content'] = htmlspecialchars($content);

		$this->_tags[] = compact('tagName', 'HTMLAttributes');
		return $this;
	}

	public function httpEquiv($header, $content, array $HTMLAttributes = [])
	{
		$tagName = 'meta';
		$HTMLAttributes['http-equiv'] = $header;
		$HTMLAttributes['content']    = htmlspecialchars($content);

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
		$content = '';
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

	public function removeHttpEquiv($httpEquiv, $content = null)
	{
		$this->_removeTag('meta', array_filter(['http-equiv' => $httpEquiv, 'content' => $content]));

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

	public function setAssetsPath($assetsPath)
	{
		$this->_assetsPath = (string)$assetsPath;

		return $this;
	}

	public function getAssetsPath($assetsPath)
	{
		return $this->_assetsPath;
	}

	private function _prepareAssetPath($file)
	{
		return (($this->_assetsPath and !parse_url($file, PHP_URL_HOST)) ? $this->_assetsPath.'/' : '') . $file;
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