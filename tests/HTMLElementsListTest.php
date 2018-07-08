<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLElementsListTest extends TestCase
{
	// Don't use constant for fake data here. HTMLElementsList::collection() wants reference.
	private $_exampleData = [
		[ 'id' => 1, 'title' => 'Title 1', 'url' => 'http://example.org/e_1', ],
		[ 'id' => 2, 'title' => 'Title 2', 'url' => 'http://example.org/e_2', ],
		[ 'id' => 3, 'title' => 'Title 3', 'url' => 'http://example.org/e_3', ],
	];

	public function testSimpleList() : void
	{
		$object = new __\HTMLElementsList('exampleClass');
		$object->collection($this->_exampleData)
			->title(function($row){ return strtoupper($row['title']); })
			->emptyMessage('Empty.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul class="exampleClass">
	<li><span>TITLE 1</span></li>
	<li><span>TITLE 2</span></li>
	<li><span>TITLE 3</span></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testEmptyList() : void
	{
		$emptyArray = [];

		$object = new __\HTMLElementsList('exampleClass');
		$object->collection($emptyArray)
			->title(function(){ return 'something'; })
			->emptyMessage('Empty.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<p class="exampleClass emptyMessage">Empty.</p>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testListWithLink() : void
	{
		$object = new __\HTMLElementsList;
		$object->collection($this->_exampleData)
			->title(function($row){ return $row['title']; })
			->link(function($row){ return $row['url']; }, ['target' => '_blank'])
			->emptyMessage('Empty.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><span><a target="_blank" href="http://example.org/e_1">Title 1</a></span></li>
	<li><span><a target="_blank" href="http://example.org/e_2">Title 2</a></span></li>
	<li><span><a target="_blank" href="http://example.org/e_3">Title 3</a></span></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testListWithRadio() : void
	{
		$object = new __\HTMLElementsList;
		$object->collection($this->_exampleData)
			->title(function($row){ return $row['title']; })
			->radio('field_name', function($row){ return $row['id']; }, 2)
			->emptyMessage('Empty.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li><span>
		<input id="field_name0" type="radio" name="field_name" value="1"><label for="field_name0">Title 1</label>
	</span></li>
	<li><span>
		<input id="field_name1" type="radio" name="field_name" checked value="2"><label for="field_name1">Title 2</label>
	</span></li>
	<li><span>
		<input id="field_name2" type="radio" name="field_name" value="3"><label for="field_name2">Title 3</label>
	</span></li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testListWithMenu() : void
	{
		$object = new __\HTMLElementsList;
		$object->collection($this->_exampleData)
			->title(function($row){ return $row['title']; })
			->menu(function($row){ return [
				['Preview', '?action=preview&id=' . $row['id']          ],
				['Edit',    '?action=edit&id='    . $row['id']          ],
				['Remove',  '?action=remove&id='  . $row['id'], 'danger'],
			]; })
			->emptyMessage('Empty.');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<ul>
	<li>
		<span>Title 1</span>
		<ul>
			<li><a aria-label="Preview — Title 1" href="?action=preview&id=1">Preview</a></li>
			<li><a aria-label="Edit — Title 1" href="?action=edit&id=1">Edit</a></li>
			<li class="danger"><a aria-label="Remove — Title 1" href="?action=remove&id=1">Remove</a></li>
		</ul>
	</li>
	<li>
		<span>Title 2</span>
		<ul>
			<li><a aria-label="Preview — Title 2" href="?action=preview&id=2">Preview</a></li>
			<li><a aria-label="Edit — Title 2" href="?action=edit&id=2">Edit</a></li>
			<li class="danger"><a aria-label="Remove — Title 2" href="?action=remove&id=2">Remove</a></li>
		</ul>
	</li>
	<li>
		<span>Title 3</span>
		<ul>
			<li><a aria-label="Preview — Title 3" href="?action=preview&id=3">Preview</a></li>
			<li><a aria-label="Edit — Title 3" href="?action=edit&id=3">Edit</a></li>
			<li class="danger"><a aria-label="Remove — Title 3" href="?action=remove&id=3">Remove</a></li>
		</ul>
	</li>
</ul>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}
}