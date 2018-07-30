<style>
	table {
		table-layout: fixed;
		text-align: left;
		border-spacing: 0 0.5em;
	}
	table th {
		font-weight: normal;
		width: 45%;
	}
	div.license {
		margin: 1em 0;
		background: #eee;
		font-size: 0.95em;
		padding: 0 1em;
		overflow-y: auto;
		max-height: 300px;
	}
	label span {
		font-size: 0.8em;
		opacity: 0.7;
		display: block;
		margin-top: 0.4em;
	}
	details {
		margin-top: 1em;
		margin-left: 1em;
	}
	summary {
		margin-left: -1em;
	}
	a.adminURL {
		display: block;
		text-align: center;
		font-weight: bold;
		font-size: 1.4em;
		background: #eee;
		line-height: 2em;
	}
</style>

<?php if ($step == 1) { ?>

	<h3>Witaj w WizyTówce!</h3>

	<p>WizyTówka to system zarządzania treścią stworzony dla małych witryn internetowych. Idealnie nadaje się do strony małej firmy czy dla&nbsp;portfolio początkującego twórcy.</p>
	<p>Masz przed oczami kreatora, który w&nbsp;kilku krokach błyskawicznie przeprowadzi cię przez proces instalacji systemu&nbsp;WizyTówka.</p>

	<table>
		<tr>
			<th>Wersja PHP na&nbsp;serwerze:</th>
			<td><?= $PHPVersion ?></td>
		</tr>
		<tr>
			<th>Oprogramowanie serwera:</th>
			<td><?= $serverSoftware ?></td>
		</tr>
		<tr>
			<th>Uprawnienia zapisu głównego folderu:</th>
			<td><?= $isDirWritable ? 'zapisywalny' : 'niezapisywalny' ?></td>
		</tr>
	</table>

	<?php if ($betaVersionWarning) { ?>
		<p class="warning">Używana wersja systemu jest wersją testową, może zawierać błędy. Nie należy używać jej do celów produkcyjnych.</p>
	<?php } ?>

	<?php if (!$isDirWritable) { ?>
		<p class="warning">Brak uprawnień do zapisu w głównym katalogu. Nie&nbsp;można zainstalować systemu WizyTówka. Aby naprawić problem, nadaj katalogowi głównemu uprawnienia <code>775</code> lub <code>777</code> poleceniem <code>chmod</code>.</p>
	<?php } ?>

	<form method="post">
		<button <?= !$isDirWritable ? 'disabled' : '' ?>>Kontynuuj</button>
		<input type="hidden" name="step" value="1">
	</form>

<?php } elseif ($step == 2) { ?>

	<h3>Licencja</h3>

	<div class="license"><?= $licenseText ?></div>

	<form method="post">
		<?= (new $formFields)
			->checkbox('Akceptuję warunki powyższej licencji', 'licenseAccepted', false, ['autofocus' => true])
			->checkbox(
				'Zezwalam na przesłanie adresu strony autorowi WizyTówki <span>Adres tworzonej właśnie strony zostanie jednokrotnie przesłany autorowi systemu WizyTówka wyłącznie w celach statystycznych.</span>',
				'sendAddressForStats', false
			)
		?>

		<button>Kontynuuj</button>
		<input type="hidden" name="step" value="2">
	</form>

<?php } elseif ($step == 3) { ?>

	<form method="post">
		<h3>Podstawowe ustawienia</h3>

		<?= (new $formFields)
			->text('Tytuł witryny', 'websiteTitle', $websiteTitle, ['required' => true, 'autofocus' => true])
			->text('Adres witryny', 'websiteAddress', $websiteAddress, ['required' => true])
		?>

		<h3>Dane użytkownika</h3>

		<?= (new $formFields)
			->text('Nazwa użytkownika', 'userName', $userName, ['required' => true])
			->password('Hasło użytkownika', 'userPasswordText_1', ['required' => true])
			->password('Hasło ponownie', 'userPasswordText_2', ['required' => true])
			->email('Adres e-mail', 'userEmail', $userEmail)
		?>

		<details>
			<summary>Ustawienia zaawansowane</summary>

			<h3>Baza danych</h3>

			<?= (new $formFields)
				->option('Użyj pliku bazy danych SQLite — opcja domyślna', 'databaseType', 'sqlite', $databaseType)
				->option('Użyj bazy danych MySQL', 'databaseType', 'mysql', $databaseType)
				->option('Użyj bazy danych PostgreSQL', 'databaseType', 'pgsql', $databaseType)
			?>
			<?= (new $formFields(false, 'databaseServiceDetails'))
				->text('Adres serwera', 'databaseHost', $databaseHost, ['required' => true])
				->text('Nazwa bazy danych', 'databaseName', $databaseName, ['required' => true])
				->text('Nazwa użytkownika', 'databaseUsername', $databaseUsername, ['required' => true])
				->text('Hasło', 'databasePassword', '')
			?>

			<script>
			(function(){
				var radioFields     = document.querySelectorAll('form input[name="databaseType"]'),
				    detailsFieldset = document.querySelector('form fieldset.databaseServiceDetails');

				function setDetailsVisibility(visible)
				{
					detailsFieldset.hidden   = !visible;
					detailsFieldset.disabled = !visible;
				}

				Array.prototype.forEach.call(radioFields, function(element){ // With <3 for Internet Explorer.
					element.addEventListener('click', function(event){
						setDetailsVisibility(event.target.value != 'sqlite');
					});

					if (element.checked) {
						setDetailsVisibility(element.value != 'sqlite');
					}
				});
			})();
			</script>

			<h3>Komunikaty błędów</h3>

			<?= (new $formFields)
				->option('Nie wyświetlaj błędów, jedynie zapisuj w&nbsp;dzienniku', 'errorsVisibility', 'none', $errorsVisibility)
				->option('Wyświetlaj błędy wyłącznie w&nbsp;panelu administracyjnym', 'errorsVisibility', 'admin', $errorsVisibility)
				->option('Zawsze wyświetlaj szczegółowe komunikaty błędów', 'errorsVisibility', 'always', $errorsVisibility)
			?>
		</details>

		<button>Zainstaluj WizyTówkę</button>
		<input type="hidden" name="step" value="3">
	</form>

<?php } elseif ($step == 4) { ?>

	<p>System WizyTówka został pomyślnie zainstalowany i&nbsp;jest&nbsp;gotowy do pracy.</p>
	<p>Możesz zarządzać swoją witryną internetową. Aby&nbsp;przejść do&nbsp;panelu administracyjnego, użyj poniższego odnośnika.</p>

	<a class="adminURL" href="admin.php">admin.php</a>

	<p>Zapisanie tego adresu w zakładkach przeglądarki może być dobrym pomysłem.</p>

<p></p>

<?php } ?>