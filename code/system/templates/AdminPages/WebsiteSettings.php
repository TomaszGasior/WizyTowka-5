<form method="post">
	<h3>Ustawienia główne</h3>

	<?= (new HTMLFormFields)
		->text('Tytuł witryny', 'nofilter_websiteTitle', HTML::escape($settings->websiteTitle), ['required'=>true])
		->text('Autor witryny', 'nofilter_websiteAuthor', HTML::escape($settings->websiteAuthor))
		->text('Układ tytułu witryny', 'nofilter_websiteTitlePattern', HTML::escape($settings->websiteTitlePattern),
			['required'=>true, 'pattern'=>'.*%s.*', 'title'=>'Użyj symbolu %s, by wskazać tytuł aktualnej strony w witrynie.']
		)
		->text('Adres witryny', 'websiteAddress', $settings->websiteAddress, ['required'=>true])
		->select('Strona główna witryny', 'websiteHomepageId', $settings->websiteHomepageId, $pagesIds)
	?>

	<h3>Typografia</h3>

	<?= (new HTMLFormFields)
		->checkbox('Przenoś jednoliterowe wyrazy z końca wiersza na początek następnego', 'typographyOrphans', $settings->typographyOrphans)
		->checkbox('Automatycznie zamieniaj cudzysłowy uproszczone na cudzysłowy polskie', 'typographyQuotes', $settings->typographyQuotes)
		->checkbox('Automatycznie zamieniaj minusy otoczone spacjami na długie pauzy', 'typographyDashes', $settings->typographyDashes)
		->checkbox('Automatycznie poprawiaj znaki wielokropka i apostrofu', 'typographyOther', $settings->typographyOther)
	?>

	<h3>Pozostałe ustawienia</h3>

	<?= (new HTMLFormFields)
		->text('Adres e-mail', 'websiteEmailAddress', $settings->websiteEmailAddress, ['required'=>true])
		->select('Format daty i godziny', 'dateTimeFormat', $dateTimeFormatSelected,
			$dateTimeFormatList, ['disabled'=>$dateTimeFormatDisable]
		)
		->checkbox('Przyjazne odnośniki', 'websitePrettyLinks', $settings->websitePrettyLinks)
	?>

	<button>Zapisz zmiany</button>
</form>