<?= (new $elementsList('elementsList'))
	->collection($pages)
	->title(function($page) use ($utils){ return $utils::correctTypography($page->title); })
	->menu(function($page) use ($adminPanelURL){ return [
		['Edytuj',      $adminPanelURL('pageEdit', ['id' => $page->id]),       'iconEdit'],
		['Właściwości', $adminPanelURL('pageProperties', ['id' => $page->id]), 'iconProperties'],
		['Opublikuj',   $adminPanelURL('pages', ['drafts' => true, 'publishId' => $page->id]),  'iconShow'],
		['Usuń',        $adminPanelURL('pages', ['drafts' => true, 'deleteId' => $page->id]),   'iconDelete deleteConfirmAlert'],
	]; })
	->emptyMessage('Nie dodano jeszcze żadnych szkiców. Szkice stron to świetne miejsce dla niedokończonych treści, nad którymi chcesz jeszcze trochę popracować — do&nbsp;momentu publikacji nie są dostępne publicznie.')
?>