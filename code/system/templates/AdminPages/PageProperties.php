<form method="post">
	<?php if ($disallowModifications) { ?>
		<p class="warning">Nie posiadasz wystarczających uprawnień, by modyfikować zawartość lub ustawienia tej strony i&nbsp;jej&nbsp;właściwości, nie będąc jej właścicielem.</p>
	<?php } ?>

	<h3>Dane strony</h3>

	<?= (new $formFields($disallowModifications))
		->text('Tytuł', 'title', $page->title, ['required' => true])
		->text('Identyfikator', 'slug', $page->slug)
		->{$hideUserIdChange ? 'skip' : 'select'}
			('Właściciel', 'userId', $page->userId, $usersIdList, ['disabled' => $disallowUserIdChange])
		->option('Strona dostępna publicznie', 'isDraft', '0', $page->isDraft, ['disabled' => $disallowPublicPage])
		->option('Szkic strony niewidoczny publicznie', 'isDraft', '1', $page->isDraft, ['disabled' => $disallowPublicPage])
	?>

	<dl>
		<dt>Data modyfikacji</dt><dd><?= $utils::formatDateTime($page->updatedTime) ?></dd>
		<dt>Data utworzenia</dt><dd><?= $utils::formatDateTime($page->createdTime) ?></dd>
	</dl>

	<p class="information">Identyfikator to uproszczona nazwa widoczna w&nbsp;adresie strony w&nbsp;pasku adresu przeglądarki. Nie może zawierać spacji, polskich znaków i&nbsp;niestandardowych symboli.</p>

	<h3>Informacje wyszukiwarek</h3>

	<?= (new $formFields($disallowModifications))
		->text('Tytuł w pasku przeglądarki', 'titleHead', $page->titleHead,
			['placeholder' => '(użyj domyślnego tytułu)'])
		->textarea('Opis dla wyszukiwarek', 'description', $page->description,
			['maxlength' => 300, 'spellcheck' => true, 'placeholder' => '(użyj domyślnego opisu witryny)'])
		->checkbox('Proś wyszukiwarki, by nie indeksowały zawartości tej strony', 'noIndex', $page->noIndex,
			['disabled' => $disableNoIndex])
	?>

	<button <?= $disallowModifications ? 'disabled' : '' ?>>Zapisz zmiany</button>
</form>