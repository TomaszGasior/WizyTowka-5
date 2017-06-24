<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($pages)
	->title(function($page){ return $page->title; })
	->link(function($page){ return WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Ustawienia', WizyTowka\AdminPanel::URL('pageSettings', ['id' => $page->id]), 'iconSettings'],
		['Edytuj', WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iconEdit'],
		['Ukryj', WizyTowka\AdminPanel::URL('pages', ['hideId' => $page->id]), 'iconHide'],
		['Usuń', WizyTowka\AdminPanel::URL('pages', ['deleteId' => $page->id]), 'iconDelete'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych stron.')
?>