<form method="post" class="PlainText">
	<?= (new WizyTowka\HTMLFormFields)
		->textarea('Zawartość', 'content', $content)
	?>

	<button>Zapisz zmiany</button>
</form>