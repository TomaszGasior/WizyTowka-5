<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($drafts)
	->title(function($page){ return $page->title; })
	->link(function($page){ return WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Ustawienia', WizyTowka\AdminPanel::URL('pageSettings', ['id' => $page->id]), 'iSettings'],
		['Edytuj', WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iEdit'],
		['Opublikuj', WizyTowka\AdminPanel::URL('drafts', ['publishId' => $page->id]), 'iShow'],
		['Usuń', WizyTowka\AdminPanel::URL('drafts', ['deleteId' => $page->id]), 'iDelete'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych szkiców stron.')
?>