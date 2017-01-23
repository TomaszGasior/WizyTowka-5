<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($pages)
	->title(function($page){ return $page->title; })
	->link(function($page){ return WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Ustawienia', WizyTowka\AdminPanel::URL('pageSettings', ['id' => $page->id]), 'iSettings'],
		['Edytuj', WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iEdit'],
		['Ukryj', WizyTowka\AdminPanel::URL('pages', ['hideId' => $page->id]), 'iHide'],
		['Usuń', WizyTowka\AdminPanel::URL('pages', ['deleteId' => $page->id]), 'iDelete'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych stron.')
?>