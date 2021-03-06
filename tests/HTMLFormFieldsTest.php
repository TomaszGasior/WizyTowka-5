<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class HTMLFormFieldsTest extends TestCase
{
	public function testText() : void
	{
		$object = new __\HTMLFormFields;
		$object->text('Example field', 'name', 'value " value');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input type="text" name="name" value="value &quot; value" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testNumber() : void
	{
		$object = new __\HTMLFormFields;
		$object->number('Example field', 'name', 8.5, ['min' => 5, 'max' => '10']);

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input min="5" max="10" type="number" name="name" value="8.5" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testColor() : void
	{
		$object = new __\HTMLFormFields;
		$object->color('Example field', 'name', '#00ff00');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input type="color" name="name" value="#00ff00" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testUrl() : void
	{
		$object1 = new __\HTMLFormFields;
		$object1->url('Example field', 'name', 'https://example.com');

		$current  = (string)$object1;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input type="url" name="name" value="https://example.com" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);

		$object2 = new __\HTMLFormFields;
		$object2->url('Example field', 'name', 'http://example.org:80');

		$current  = (string)$object2;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input type="url" name="name" value="http://example.org:80" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testEmail() : void
	{
		$object = new __\HTMLFormFields;
		$object->email('Example field', 'name', 'email@example.com');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input type="email" name="name" value="email@example.com" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testPassword() : void
	{
		$object = new __\HTMLFormFields;
		$object->password('Example field', 'name', ['required' => true, 'disabled' => false]);

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><input required type="password" name="name" id="name"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testCheckbox() : void
	{
		$object = new __\HTMLFormFields;
		$object->checkbox('Example checkbox', 'name_1', true);
		$object->checkbox('Example checkbox', 'name_2', false);

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div class="checkable">
		<input type="checkbox" name="name_1" checked id="name_1">
		<label for="name_1">Example checkbox</label>
	</div>
	<div class="checkable">
		<input type="checkbox" name="name_2" id="name_2">
		<label for="name_2">Example checkbox</label>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRadio() : void
	{
		$selectedValue = 'linux';
		$object = new __\HTMLFormFields;
		$object->radio('I use Windows', 'operating_system', 'windows', $selectedValue);
		$object->radio('I use Linux', 'operating_system', 'linux', $selectedValue);
		$object->option('I use Mac OS', 'operating_system', 'mac_os', $selectedValue);  // "option" is an alias of "radio".

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div class="checkable">
		<input type="radio" name="operating_system" value="windows" id="operating_system_windows">
		<label for="operating_system_windows">I use Windows</label>
	</div>
	<div class="checkable">
		<input type="radio" name="operating_system" value="linux" checked id="operating_system_linux">
		<label for="operating_system_linux">I use Linux</label>
	</div>
	<div class="checkable">
		<input type="radio" name="operating_system" value="mac_os" id="operating_system_mac_os">
		<label for="operating_system_mac_os">I use Mac OS</label>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testTextarea() : void
	{
		$object = new __\HTMLFormFields;
		$object->textarea('Example field', 'name', "Line 1.\nLine 2.\nLine 3.");

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset>
	<div>
		<label for="name">Example field</label>
		<span><textarea name="name" id="name">Line 1.
Line 2.
Line 3.</textarea></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testSelect() : void
	{
		$object = new __\HTMLFormFields;
		$object->select('Select your OS', 'operating_system', 'linux', [
			'windows' => 'Windows',
			'linux' => 'Linux',
			'mac_os' => 'Mac OS',
		]);

		$current  = (string)$object;
		$expected = <<< 'HTML'
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
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testTextWithHints() : void
	{
		$object = new __\HTMLFormFields;
		$object->textWithHints('Select your OS', 'operating_system', '', [
			'windows' => 'Windows',
			'linux' => 'Linux',
			'mac_os' => 'Mac OS',
		]);

		$current  = (string)$object;
		$expected = <<< 'HTML'
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
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testRemove() : void
	{
		$object = new __\HTMLFormFields(false, 'exampleCSSClass');
		$object->text('Example field 1.', 'name_1', 'value');
		$object->text('Example field 2.', 'name_2', 'value');
		$object->text('Example field 2.', 'name_2', 'value');
		$object->text('Example field 3.', 'name_3', 'value');
		$object->remove('name_2');

		$current  = (string)$object;
		$expected = <<< 'HTML'
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
HTML;
		$this->assertHTMLEquals($expected, $current);
	}

	public function testDisabled() : void
	{
		$object = new __\HTMLFormFields(true, 'exampleCSSClass');
		$object->text('Example field 1.', 'name_1', 'value');
		$object->text('Example field 2.', 'name_2', 'value');

		$current  = (string)$object;
		$expected = <<< 'HTML'
<fieldset disabled class="exampleCSSClass">
	<div>
		<label for="name_1">Example field 1.</label>
		<span><input type="text" name="name_1" value="value" id="name_1"></span>
	</div>
	<div>
		<label for="name_2">Example field 2.</label>
		<span><input type="text" name="name_2" value="value" id="name_2"></span>
	</div>
</fieldset>
HTML;
		$this->assertHTMLEquals($expected, $current);
	}
}