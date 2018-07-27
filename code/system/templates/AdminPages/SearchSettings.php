<form method="post">
	<h3>Domyślne informacje wyszukiwarek</h3>

	<?= (new $formFields)
		->textarea('Opis witryny', 'searchEnginesDescription', $settings->searchEnginesDescription,
			['maxlength' => 300, 'spellcheck' => true])
		->checkbox('Proś wyszukiwarki, by nie indeksowały zawartości witryny', 'robots[noindex]', $robots['noindex'])
	?>

	<p class="information">Opis dla wyszukiwarek można ustawić także indywidualnie dla każdej strony — zastąpi on&nbsp;wtedy opis witryny; powinien mieć maksymalnie 300 znaków. Istnieje też możliwość wykluczenia z indeksowania wybranej strony.</p>

	<h3>Dodatkowe globalne opcje indeksowania</h3>

	<?= (new $formFields)
		->checkbox('Proś wyszukiwarki, by nie indeksowały obrazków umieszczanych w witrynie', 'robots[noimageindex]', $robots['noimageindex'])
		->checkbox('Proś wyszukiwarki, by nie archiwizowały kopii zawartości witryny', 'robots[noarchive]', $robots['noarchive'])
		->checkbox('Proś wyszukiwarki, by nie podążały za odnośnikami w witrynie', 'robots[nofollow]', $robots['nofollow'])
	?>

	<button>Zapisz zmiany</button>
</form>