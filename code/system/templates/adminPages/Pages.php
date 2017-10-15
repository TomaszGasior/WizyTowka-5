<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($pages)
	->title(function($page){ return $page->title; })
	->link(function($page){ return WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Edytuj', WizyTowka\AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iconEdit'],
		['Właściwości', WizyTowka\AdminPanel::URL('pageProperties', ['id' => $page->id]), 'iconSettings'],
		['Ukryj', WizyTowka\AdminPanel::URL('pages', ['hideId' => $page->id]), 'iconHide'],
		['Usuń', WizyTowka\AdminPanel::URL('pages', ['deleteId' => $page->id]), 'iconDelete'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych stron.')
?>