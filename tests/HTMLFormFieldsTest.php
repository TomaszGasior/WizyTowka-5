<?php

/**
* WizyTówka 5 — unit test
*/
class HTMLFormFieldsTest extends PHPUnit\Framework\TestCase
{
	private function assertHTMLEquals($expected, $current, $message = null)
	{
		$this->assertXmlStringEqualsXmlString(
			(@DOMDocument::loadHTML($expected, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
			(@DOMDocument::loadHTML($current,  LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD))->saveXML(),
			$message
		);
	}

	public function testText()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->text('Example field', 'name', 'value');

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input type="text" name="name" value="value" id="name"></span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testNumber()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->number('Example field', 'name', 8.5, ['min' => 5, 'max' => '10']);

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input min="5" max="10" type="number" name="name" value="8.5" id="name"></span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testPassword()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->password('Example field', 'name', ['required' => true, 'disabled' => false]);

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input required type="password" name="name" id="name"></span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testCheckbox()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->checkbox('Example checkbox', 'name_1', true);
		$object->checkbox('Example checkbox', 'name_2', false);

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<input type="checkbox" name="name_1" checked id="name_1">
		<label for="name_1">Example checkbox</label>
	</div>
	<div>
		<input type="checkbox" name="name_2" id="name_2">
		<label for="name_2">Example checkbox</label>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRadio()
	{
		$selectedValue = 'linux';
		$object = new WizyTowka\HTMLFormFields;
		$object->radio('I use Windows', 'operating_system', 'windows', $selectedValue);
		$object->radio('I use Linux', 'operating_system', 'linux', $selectedValue);
		$object->option('I use Mac OS', 'operating_system', 'mac_os', $selectedValue);  // "option" is an alias of "radio".

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<input type="radio" name="operating_system" value="windows" id="operating_system_windows">
		<label for="operating_system_windows">I use Windows</label>
	</div>
	<div>
		<input type="radio" name="operating_system" value="linux" checked id="operating_system_linux">
		<label for="operating_system_linux">I use Linux</label>
	</div>
	<div>
		<input type="radio" name="operating_system" value="mac_os" id="operating_system_mac_os">
		<label for="operating_system_mac_os">I use Mac OS</label>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testTextarea()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->textarea('Example field', 'name', "Line 1.\nLine 2.\nLine 3.");

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><textarea name="name" id="name">Line 1.
Line 2.
Line 3.</textarea></span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testSelect()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->select('Select your OS', 'operating_system', 'linux', [
			'windows' => 'Windows',
			'linux' => 'Linux',
			'mac_os' => 'Mac OS',
		]);

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<label for="operating_system">Select your OS</label>
		<span>
			<select name="operating_system" id="operating_system">
				<option value="windows">Windows</option>
				<option value="linux" selected>Linux</option>
				<option value="mac_os">Mac OS</option>
			</select>
		</span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testTextWithHints()
	{
		$object = new WizyTowka\HTMLFormFields;
		$object->textWithHints('Select your OS', 'operating_system', '', [
			'windows' => 'Windows',
			'linux' => 'Linux',
			'mac_os' => 'Mac OS',
		]);

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset>
	<div>
		<label for="operating_system">Select your OS</label>
		<span>
			<datalist id="hints_operating_system">
				<option>Windows</option>
				<option>Linux</option>
				<option>Mac OS</option>
			</datalist>
			<input type="text" name="operating_system" value="" id="operating_system" list="hints_operating_system">
		</span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRemove()
	{
		$object = new WizyTowka\HTMLFormFields('exampleCSSClass');
		$object->text('Example field 1.', 'name_1', 'value');
		$object->text('Example field 2.', 'name_2', 'value');
		$object->text('Example field 2.', 'name_2', 'value');
		$object->text('Example field 3.', 'name_3', 'value');
		$object->remove('name_2');

		$current = (string)$object;
		$expected = <<< 'EOL'
<fieldset class="exampleCSSClass">
	<div>
		<label for="name_1">Example field 1.</label>
		<span><input type="text" name="name_1" value="value" id="name_1"></span>
	</div>
	<div>
		<label for="name_3">Example field 3.</label>
		<span><input type="text" name="name_3" value="value" id="name_3"></span>
	</div>
</fieldset>
EOL;
		$this->assertHTMLEquals($expected, $current);
	}
}