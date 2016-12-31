<h3>Lista elementów</h3>

<ul class="elementsList tableView">
<li>
	<span><a href="#">Przykładowa strona</a></span>
	<ul>
		<li class="iSettings"><a href="#"><span>Przykładowa strona — </span>Ustawienia</a></li>
		<li class="iEdit"><a href="#"><span>Przykładowa strona — </span>Edytuj</a></li>
		<li class="iHide"><a href="#"><span>Przykładowa strona — </span>Ukryj</a></li>
		<li class="iDelete"><a href="#"><span>Przykładowa strona — </span>Usuń</a></li>
	</ul>
</li><li>
	<span>Przykładowa strona</span>
	<ul>
		<li class="iSettings"><a href="#"><span>Przykładowa strona — </span>Ustawienia</a></li>
		<li class="iEdit"><a href="#"><span>Przykładowa strona — </span>Edytuj</a></li>
		<li class="iHide"><a href="#"><span>Przykładowa strona — </span>Ukryj</a></li>
		<li class="iDelete"><a href="#"><span>Przykładowa strona — </span>Usuń</a></li>
	</ul>
</li><li>
	<span>Przykładowa strona</span>
	<ul>
		<li class="iSettings"><a href="#"><span>Przykładowa strona — </span>Ustawienia</a></li>
		<li class="iEdit"><a href="#"><span>Przykładowa strona — </span>Edytuj</a></li>
		<li class="iHide"><a href="#"><span>Przykładowa strona — </span>Ukryj</a></li>
		<li class="iDelete"><a href="#"><span>Przykładowa strona — </span>Usuń</a></li>
	</ul>
</li><li>
	<span>Przykładowa strona</span>
	<ul>
		<li class="iSettings"><a href="#"><span>Przykładowa strona — </span>Ustawienia</a></li>
		<li class="iEdit"><a href="#"><span>Przykładowa strona — </span>Edytuj</a></li>
		<li class="iHide"><a href="#"><span>Przykładowa strona — </span>Ukryj</a></li>
		<li class="iDelete"><a href="#"><span>Przykładowa strona — </span>Usuń</a></li>
	</ul>
</li>
</ul>

<h3>Formularz</h3>

<form method="post">
	<dl class="form">
		<?= (new WizyTowka\HTMLFormFields)
			->text('Imię i nazwisko', 'name', '')
			->select('Język', 'lang', 'pl', [
				'en' => 'Angielski',
				'pl' => 'Polski',
			])
			->number('Liczba podstron', 'pagesnumber', '')
		?>
	</dl>

	<p class="warning">Litwo! Ojczyzno moja! Ty jesteś jak zdrowie. Nazywał się ranną. Skromny młodzieniec oczy podniósł, i stajennym i z rozsądkiem wiedział, czy pod Twoją opiek ofiarowany, martwą podniosłem powiek i Bernatowicze, Kupść, Gedymin i posępny obok Jegomościa.</p>

	<p class="information">Litwo! Ojczyzno moja! Ty jesteś jak zdrowie. Nazywał się ranną. Skromny młodzieniec oczy podniósł, i stajennym i z rozsądkiem wiedział, czy pod Twoją opiek ofiarowany, martwą podniosłem powiek i Bernatowicze, Kupść, Gedymin i posępny obok Jegomościa.</p>

	<button>Zapisz</button>
</form>