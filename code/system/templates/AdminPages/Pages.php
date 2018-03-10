<?= (new HTMLElementsList('elementsList'))
	->collection($pages)
	->title(function($page){ return HTML::correctTypography($page->title); })
	->link(function($page){ return Website::URL($page->slug); }, ['target' => '_blank'])
	->menu(function($page){ return [
		['Edytuj',      AdminPanel::URL('pageEdit', ['id' => $page->id]),       'iconEdit'],
		['Właściwości', AdminPanel::URL('pageProperties', ['id' => $page->id]), 'iconProperties'],
		['Ukryj',       AdminPanel::URL('pages', ['hideId' => $page->id]),      'iconHide'],
		['Usuń',        AdminPanel::URL('pages', ['deleteId' => $page->id]),    'iconDelete deleteConfirmAlert'],
	]; })
?>