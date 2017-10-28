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
		$this->_HTMLHead->inlineStylesheet(
			<<< CSS
form.PlainText > fieldset > div, form.PlainText > fieldset > div > * {
	display: block;
}
form.PlainText label {
	clip: rect(1px 1px 1px 1px);
	position: absolute;
}
form.PlainText textarea {
	min-height: 400px;
	font-family: monospace;
}
CSS
		);

		$this->_HTMLTemplate->content = $this->_contents->html;
	}
}