<?php

/**
* WizyTówka 5
* Generator of form fieldsets HTML code.
*/
namespace WizyTowka;

class HTMLFormFields
{
	private $_fieldsetCSSClass;
	private $_fields = [];

	public function __construct($CSSClass = null)
	{
		$this->_fieldsetCSSClass = $CSSClass;
	}

	public function __debugInfo()
	{
		return $this->_fields;
	}

	public function text($label, $name, $value, array $HTMLAttributes = [])
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = str_replace("\n", null, $value);

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function number($label, $name, $value, array $HTMLAttributes = [])
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = is_numeric($value) ? str_replace(',', '.', $value) : '0';
		// Number can be wrong formatted because of locale settings.

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function color($label, $name, $value, array $HTMLAttributes = [])
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = preg_match('/#[0-9a-f]{6}/i', $value) ? $value : '#ffffff';

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function url($label, $name, $value, array $HTMLAttributes = [])
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = filter_var($value, FILTER_VALIDATE_URL) ? $value : '';

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function email($label, $name, $value, array $HTMLAttributes = [])
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : '';

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function password($label, $name, array $HTMLAttributes = [])
	{
		$type = 'simple';
		$HTMLAttributes['type']  = __FUNCTION__;
		$HTMLAttributes['name']  = $name;
		unset($HTMLAttributes['value']);
		// Don't allow predefined value on password field.

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function checkbox($label, $name, $currentValue, array $HTMLAttributes = [])
	{
		$type = 'checkable';
		$HTMLAttributes['type']    = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['checked'] = (bool)$currentValue;

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function radio($label, $name, $fieldValue, $currentValue, array $HTMLAttributes = [])
	{
		$type = 'checkable';
		$HTMLAttributes['type']    = __FUNCTION__;
		$HTMLAttributes['name']    = $name;
		$HTMLAttributes['value']   = $fieldValue;
		$HTMLAttributes['checked'] = ($currentValue == $fieldValue);

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label');
		return $this;
	}

	public function option(...$arguments)
	{
		return $this->radio(...$arguments);
	}

	public function textarea($label, $name, $content, array $HTMLAttributes = [])
	{
		$type = __FUNCTION__;
		$HTMLAttributes['name'] = $name;

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label', 'content');
		return $this;
	}

	public function select($label, $name, $selected, array $valuesList, array $HTMLAttributes = [])
	{
		$type = __FUNCTION__;
		$HTMLAttributes['name']  = $name;

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label', 'valuesList', 'selected');
		return $this;
	}

	public function textWithHints($label, $name, $value, array $hints, array $HTMLAttributes = [])
	{
		$type = __FUNCTION__;
		$HTMLAttributes['type']  = 'text';
		$HTMLAttributes['name']  = $name;
		$HTMLAttributes['value'] = str_replace("\n", null, $value);

		$this->_fields[] = compact('type', 'HTMLAttributes', 'label', 'hints');
		return $this;
	}

	public function remove($name)
	{
		foreach ($this->_fields as $key => $field) {
			if ($field['HTMLAttributes']['name'] == $name) {
				unset($this->_fields[$key]);
			}
		}

		return $this;
	}

	public function __toString()
	{
		ob_start();

		echo '<fieldset', $this->_fieldsetCSSClass ? ' class="'.$this->_fieldsetCSSClass.'">' : '>';

		foreach ($this->_fields as $field) {
			$id = $field['HTMLAttributes']['name'] .
				(($field['type']=='checkable' and $field['HTMLAttributes']['type']=='radio') ? '_'.$field['HTMLAttributes']['value'] : '');
			$field['HTMLAttributes']['id'] = $id;   // Unique ID is used to assign form control to label.

			echo '<div>';

			if ($field['type'] == 'checkable') {
				$this->_renderHTMLOpenTag('input', $field['HTMLAttributes']);
				echo '<label for="', $id, '">', $field['label'], '</label>';
			}
			else {
				echo '<label for="', $id, '">', $field['label'], '</label>';
				echo '<span>';

				switch ($field['type']) {
					case 'textWithHints':
						$hintsId = 'hints_' . $id;
						echo '<datalist id="', $hintsId, '">';
						foreach ($field['hints'] as $hint) {
							echo '<option>', $hint, '</option>';
						}
						echo '</datalist>';
						$field['HTMLAttributes']['list'] = $hintsId;
						// No break. Share code with standard input.

					case 'simple':
						$this->_renderHTMLOpenTag('input', $field['HTMLAttributes']);
						break;

					case 'textarea':
						$this->_renderHTMLOpenTag('textarea', $field['HTMLAttributes']);
						echo $field['content'], '</textarea>';
						break;

					case 'select':
						$this->_renderHTMLOpenTag('select', $field['HTMLAttributes']);
						foreach ($field['valuesList'] as $value => $label) {
							echo '<option value="', $value, ($value == $field['selected']) ? '" selected>' : '">', $label, '</option>';
						}
						echo '</select>';
				}

				echo '</span>';
			}

			echo '</div>';
		}

		echo '</fieldset>';

		return ob_get_clean();
	}

	private function _renderHTMLOpenTag($tagName, array $attributes = [])
	{
		echo '<', $tagName;
		foreach ($attributes as $name => $value) {
			if ($value === false) {
				continue;
			}
			echo ' ', $name;
			if ($value !== true) {
				echo '="', $value, '"';
			}
		}
		echo '>';
	}
}