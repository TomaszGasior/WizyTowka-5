<?= (new HTMLElementsList('elementsList'))
	->collection($pages)
	->title(function($page){ return HTML::correctTypography($page->title); })
	->menu(function($page){ return [
		['Edytuj',      AdminPanel::URL('pageEdit', ['id' => $page->id]),       'iconEdit'],
		['Właściwości', AdminPanel::URL('pageProperties', ['id' => $page->id]), 'iconProperties'],
		['Opublikuj',   AdminPanel::URL('pages', ['drafts' => true, 'publishId' => $page->id]),  'iconShow'],
		['Usuń',        AdminPanel::URL('pages', ['drafts' => true, 'deleteId' => $page->id]),   'iconDelete deleteConfirmAlert'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych szkiców. Szkice stron to świetne miejsce dla niedokończonych treści, nad którymi chcesz jeszcze trochę popracować — do&nbsp;momentu publikacji nie są dostępne publicznie.')
?>