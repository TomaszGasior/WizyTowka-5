<?php if ($errorsList) { ?>
	<p class="screenReaders">Szczegóły napotkanych problemów.</p>
	<ul>
		<?php foreach ($errorsList as $file => $error) { ?>
			<li>„<?= $file ?>” — <?= $error ?></li>
		<?php } ?>
	</ul>
<?php } ?>

<form method="post" enctype="multipart/form-data">
	<fieldset <?= $featureDisabled ? 'disabled' : '' ?>>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxFileSize ?>">

		<div>
			<label for="filePicker" class="screenReaders">Wskaż pliki do przesłania</label>
			<input id="filePicker" name="sendingFiles[]" type="file" multiple required>
		</div>
	</fieldset>

	<?php if ($featureDisabled) { ?>
		<p class="warning">Możliwość wysyłania plików jest zablokowana w konfiguracji interpretera PHP. Skontaktuj się z&nbsp;administratorem serwera, by uzyskać pomoc.</p>
	<?php } ?>

	<?php if ($maxFileSize) { ?>
		<p class="information">Maksymalna wielkość jednego pliku to <?= $utils::formatFileSize($maxFileSize) ?>.</p>
	<?php } ?>

	<?php if ($maxFilesNumber > 1) { ?>
		<p class="information">Jednocześnie można przesłać <?= $maxFilesNumber ?> plików. W&nbsp;celu wskazania kilku plików należy przeciągnąć zaznaczenie myszką lub wskazywać każdy oddzielnie, trzymając wciśnięty przycisk <code>Ctrl</code>.</p>
	<?php } ?>

	<button <?= $featureDisabled ? 'disabled' : '' ?>>Wyślij pliki</button>
</form>