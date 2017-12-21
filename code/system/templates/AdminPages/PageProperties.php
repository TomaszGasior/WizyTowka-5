<form method="post">
	<?php if ($permissionLimitNotification) { ?>
		<p class="warning">Nie posiadasz wystarczających uprawnień, by modyfikować zawartość lub ustawienia tej strony i&nbsp;jej&nbsp;właściwości, nie będąc jej właścicielem.</p>
	<?php } ?>

	<h3>Dane strony</h3>

	<?= (new HTMLFormFields)
		->text('Tytuł', 'title', $page->title, ['required'=>true])
		->text('Identyfikator', 'nofilter_slug', $page->slug)
		->select('Właściciel', 'userId', $page->userId, $usersIdList, ['disabled' => $disableUserIdChange])
		->option('Strona dostępna publicznie', 'isDraft', '0', $page->isDraft)
		->option('Szkic strony niewidoczny publicznie', 'isDraft', '1', $page->isDraft)
	?>

	<p class="information">Identyfikator to uproszczona nazwa widoczna w&nbsp;adresie strony w&nbsp;pasku adresu przeglądarki. Nie może zawierać spacji, polskich znaków i&nbsp;niestandardowych symboli.</p>

	<h3>Informacje dla wyszukiwarek</h3>

	<?= (new HTMLFormFields)
		->textarea('Opis', 'description', $page->description, ['maxlength'=>500, 'spellcheck'=>true])
		->textarea('Słowa kluczowe', 'keywords', $page->keywords, ['maxlength'=>500, 'spellcheck'=>true])
	?>

	<button <?= $disableSaveButton ? 'disabled' : '' ?>>Zapisz zmiany</button>
</form>