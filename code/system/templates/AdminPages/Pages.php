<?= (new HTMLElementsList('elementsList listView'))
	->collection($pages)
	->title(function($page){ return $page->title; })
	->link(function($page){ return AdminPanel::URL('pageEdit', ['id' => $page->id]); })
	->menu(function($page){ return [
		['Edytuj', AdminPanel::URL('pageEdit', ['id' => $page->id]), 'iconEdit'],
		['Właściwości', AdminPanel::URL('pageProperties', ['id' => $page->id]), 'iconSettings'],
		['Ukryj', AdminPanel::URL('pages', ['hideId' => $page->id]), 'iconHide'],
		['Usuń', AdminPanel::URL('pages', ['deleteId' => $page->id]), 'iconDelete'],
	]; })
?>