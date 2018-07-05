<?php

/**
* WizyTówka 5
* Plain text content type — editor page.
*/
namespace WizyTowka\PlainText;
use WizyTowka as __;

class EditorPage extends __\ContentTypeAPI
{
	public function POSTQuery() : void
	{
		$this->_contents->html = $_POST['content'];
	}

	public function HTMLContent() : void
	{
		$this->_HTMLHead->stylesheet('AdminPanelFix.css');

		$this->_HTMLTemplate->content = $this->_contents->html;
	}
}