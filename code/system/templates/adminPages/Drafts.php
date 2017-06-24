<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($drafts)
	->title(function($page){ return $page->title; })
	->link(function($page){ return WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Ustawienia', WizyTowka\AdminPanel::URL('pageSettings', ['id' => $page->id]), 'iconSettings'],
		['Edytuj', WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iconEdit'],
		['Opublikuj', WizyTowka\AdminPanel::URL('drafts', ['publishId' => $page->id]), 'iconShow'],
		['Usuń', WizyTowka\AdminPanel::URL('drafts', ['deleteId' => $page->id]), 'iconDelete'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych szkiców stron.')
?>