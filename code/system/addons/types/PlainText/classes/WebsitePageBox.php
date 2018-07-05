<?php

/**
* WizyTówka 5
* Plain text content type — website page box.
*/
namespace WizyTowka\PlainText;
use WizyTowka as __;

class WebsitePageBox extends __\ContentTypeAPI
{
	public function HTMLContent() : void
	{
		$this->_HTMLTemplate->setRaw('content', $this->_contents->html);
	}
}