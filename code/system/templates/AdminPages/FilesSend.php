<?php if ($errorsList) { ?>
	<p class="screenReaders">Szczegóły napotkanych problemów.</p>
	<ul>
		<?php foreach ($errorsList as $file => $error) { ?>
			<li>„<?= $file ?>” — <?= $error ?></li>
		<?php } ?>
	</ul>
<?php } ?>

<form method="post" enctype="multipart/form-data">
	<fieldset>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxFileSize ?>">

		<label for="filePicker" class="screenReaders">Wskaż pliki do przesłania</label>
		<input id="filePicker" name="sendingFiles[]" type="file" multiple required>
	</fieldset>

	<p class="information">
		Jeśli przeglądarka internetowa umożliwia wysyłanie wielu plików jednocześnie, w celu wskazania kilku plików należy przeciągnąć zaznaczenie myszką lub wskazywać każdy oddzielnie, trzymając wciśnięty przycisk <code>Ctrl</code>.
	</p>

	<button>Wyślij pliki</button>
</form>