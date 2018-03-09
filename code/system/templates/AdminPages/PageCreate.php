<form method="post">
	<h3>Dane strony</h3>

	<?= (new HTMLFormFields)
		->text('Tytuł', 'title', '', ['required' => true])
		->text('Identyfikator', 'slug', '')
		->option('Strona dostępna publicznie', 'isDraft', '0', $autocheckDraft, ['disabled' => $disallowPublicPage])
		->option('Szkic strony niewidoczny publicznie', 'isDraft', '1', $autocheckDraft, ['disabled' => $disallowPublicPage])
	?>

	<p class="information">Identyfikator to uproszczona nazwa widoczna w&nbsp;adresie strony w&nbsp;pasku adresu przeglądarki. Nie może zawierać spacji, polskich znaków i&nbsp;niestandardowych symboli. Nie musisz go uzupełniać — zostanie wygenerowany automatycznie na podstawie tytułu.</p>

	<h3>Typ zawartości</h3>

	<?= (new HTMLElementsList('elementsList'))
		->collection($contentTypes)
		->title(function($type){ return $type->label; })
		->option('type', function($type){ return $type->name; }, $autocheckContentType)
	?>

	<button>Utwórz stronę</button>
</form>

<script>
(function(){
	// Automatically update admin page title when user changes $isDraft page property.
	var titlePage  = 'Utwórz stronę',
	    titleDraft = 'Utwórz szkic strony';

	var elements    = document.querySelectorAll('head > title, main > header > h2, form[method="post"] button'),
	    radioFields = document.querySelectorAll('form input[name="isDraft"]');

	function updateTitle(isDraft)
	{
		var replaceFrom = isDraft ? titlePage : titleDraft,
		    replaceTo   = !isDraft ? titlePage : titleDraft;

		elements.forEach(function(element){
			element.innerHTML = element.innerHTML.replace(replaceFrom, replaceTo);
		})
	}

	radioFields.forEach(function(element){
		element.addEventListener('click', function(event){
			updateTitle(event.target.value == 1);
		});

		if (element.checked) {
			updateTitle(element.value == 1);
		}
	});
})();
</script>