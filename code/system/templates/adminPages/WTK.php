<p class="warning">Niepoprawna modyfikacja pliku konfiguracyjnego systemu może zagrozić jego stabilności. Korzystaj z&nbsp;tego narzędzia jedynie, gdy dokładnie wiesz, co robisz.</p>

<form method="post">
	<?php
	$fields = new WizyTowka\HTMLFormFields;
	foreach ($settings as $name => $value) {
		$fields->text(
			$name, $name, $value,
			isset($defaultSettings->$name) ? ['title' => 'Domyślna wartość: ' . $defaultSettings->$name] : []
		);
	}
	?>
	<?= $fields ?>

	<button>Zapisz zmiany</button>
</form>