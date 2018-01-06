<form method="post">
	<h3>Dane strony</h3>

	<?= (new HTMLFormFields)
		->text('Tytuł', 'nofilter_title', '', ['required' => true])
		->text('Identyfikator', 'nofilter_slug', '')
		->option('Strona dostępna publicznie', 'isDraft', '0', $autocheckDraft)
		->option('Szkic strony niewidoczny publicznie', 'isDraft', '1', $autocheckDraft)
	?>

	<p class="information">Identyfikator to uproszczona nazwa widoczna w&nbsp;adresie strony w&nbsp;pasku adresu przeglądarki. Nie może zawierać spacji, polskich znaków i&nbsp;niestandardowych symboli. Nie musisz go uzupełniać — zostanie wygenerowany automatycznie na podstawie tytułu.</p>

	<h3>Typ zawartości</h3>

	<?= (new HTMLElementsList('elementsList listView'))
		->collection($contentTypes)
		->title(function($type){ return $type->label; })
		->option('type', function($type){ return $type->getName(); }, $autocheckContentType)
	?>

	<button>Utwórz stronę</button>
</form>