<form method="post">
	<h3>Ustawienia główne</h3>

	<?= (new $formFields)
		->text('Tytuł witryny', 'websiteTitle', $settings->websiteTitle, ['required' => true])
		->text('Autor witryny', 'websiteAuthor', $settings->websiteAuthor)
		->text('Układ tytułu witryny', 'websiteTitlePattern', $settings->websiteTitlePattern,
			['required' => true, 'pattern' => '.*%s.*', 'title' => 'Użyj symbolu %s, by wskazać tytuł aktualnej strony w witrynie.']
		)
		->text('Adres witryny', 'websiteAddress', $settings->websiteAddress, ['required' => true])
		->select('Strona główna witryny', 'websiteHomepageId', $settings->websiteHomepageId, $pagesIds)
	?>

	<h3>Typografia</h3>

	<?= (new $formFields)
		->checkbox('Przenoś jednoliterowe wyrazy z&nbsp;końca wiersza na początek następnego', 'typographyOrphans', $settings->typographyOrphans)
		->checkbox('Automatycznie zamieniaj cudzysłowy uproszczone na cudzysłowy polskie', 'typographyQuotes', $settings->typographyQuotes)
		->checkbox('Automatycznie zamieniaj minusy otoczone spacjami na długie pauzy', 'typographyDashes', $settings->typographyDashes)
		->checkbox('Automatycznie poprawiaj znaki wielokropka i&nbsp;apostrofu', 'typographyOther', $settings->typographyOther)
	?>

	<h3>Pozostałe ustawienia</h3>

	<?= (new $formFields)
		->email('Adres e-mail', 'websiteEmailAddress', $settings->websiteEmailAddress, ['required' => true])
		->select('Format daty i godziny', 'dateTimeFormat', $dateTimeFormatSelected, $dateTimeFormatList,
			['disabled' => $dateTimeFormatDisable]
		)
		->checkbox('Przyjazne odnośniki', 'websitePrettyLinks', $settings->websitePrettyLinks)
	?>

	<button>Zapisz zmiany</button>
</form>