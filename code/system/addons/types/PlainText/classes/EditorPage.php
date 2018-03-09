<?php

/**
* WizyTówka 5
* Plain text content type — editor page.
*/
namespace WizyTowka\PlainText;
use WizyTowka as WT;

class EditorPage extends WT\ContentTypeAPI
{
	public function POSTQuery()
	{
		$this->_contents->html = $_POST['content'];
	}

	public function HTMLContent()
	{
		$this->_HTMLHead->stylesheet('AdminPanelFix.css');

		$this->_HTMLTemplate->content = $this->_contents->html;
	}
}