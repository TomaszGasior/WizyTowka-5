<form method="post">
	<h3>Ustawienia główne</h3>

	<?= (new HTMLFormFields)
		->text('Tytuł witryny', 'websiteTitle', $settings->websiteTitle, ['required'=>true])
		->text('Autor witryny', 'websiteAuthor', $settings->websiteAuthor)
		->text('Układ tytułu witryny', 'websiteTitlePattern', $settings->websiteTitlePattern,
			['required'=>true, 'pattern'=>'.*%s.*', 'title'=>'Użyj symbolu %s, by wskazać tytuł aktualnej strony w witrynie.']
		)
		->text('Adres witryny', 'websiteAddress', $settings->websiteAddress, ['required'=>true])
		->select('Strona główna witryny', 'websiteHomepageId', $settings->websiteHomepageId, $pagesIds)
	?>

	<h3>Pozostałe ustawienia</h3>

	<?= (new HTMLFormFields)
		->text('Adres e-mail', 'websiteEmailAddress', $settings->websiteEmailAddress, ['required'=>true])
		->select('Format daty i godziny', 'websiteDateFormat', $settings->websiteDateFormat,
			$dateFormatsAndExamples, ['disabled'=>$disableDateFormatField]
		)
		->checkbox('Przyjazne odnośniki', 'websitePrettyLinks', $settings->websitePrettyLinks)
	?>

	<button>Zapisz zmiany</button>
</form>