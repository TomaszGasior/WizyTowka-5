<?= (new HTMLElementsList('elementsList listView'))
	->collection($drafts)
	->title(function($page){ return $page->title; })
	->link(function($page){ return AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Edytuj', AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iconEdit'],
		['Właściwości', AdminPanel::URL('pageProperties', ['id' => $page->id]), 'iconSettings'],
		['Opublikuj', AdminPanel::URL('drafts', ['publishId' => $page->id]), 'iconShow'],
		['Usuń', AdminPanel::URL('drafts', ['deleteId' => $page->id]), 'iconDelete'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych szkiców. Szkice stron to świetne miejsce dla niedokończonych treści, nad którymi chcesz jeszcze trochę popracować — do&nbsp;momentu publikacji nie są dostępne publicznie.')
?>