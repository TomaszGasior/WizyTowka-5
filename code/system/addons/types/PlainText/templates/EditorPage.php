<form method="post" class="PlainText">
	<?= (new WizyTowka\HTMLFormFields)
		->textarea('Zawartość', 'nofilter_content', $content)
	?>

	<button>Zapisz zmiany</button>
</form>