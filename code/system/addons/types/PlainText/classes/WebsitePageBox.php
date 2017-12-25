<?php

/**
* WizyTówka 5
* Plain text content type — website page box.
*/
namespace WizyTowka\PlainText;
use WizyTowka as WT;

class WebsitePageBox extends WT\ContentTypeAPI
{
	public function HTMLContent()
	{
		$this->_HTMLTemplate->content = $this->_contents->html;
	}
}