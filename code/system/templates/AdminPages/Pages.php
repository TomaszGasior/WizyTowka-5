<?= (new $elementsList('elementsList'))
	->collection($pages)
	->title(function($page) use ($utils){ return $utils::correctTypography($page->title); })
	->link(function($page) use ($websiteURL){ return $websiteURL($page->slug); }, ['target' => '_blank'])
	->menu(function($page) use ($adminPanelURL){ return [
		['Edytuj',      $adminPanelURL('pageEdit', ['id' => $page->id]),       'iconEdit'],
		['Właściwości', $adminPanelURL('pageProperties', ['id' => $page->id]), 'iconProperties'],
		['Ukryj',       $adminPanelURL('pages', ['hideId' => $page->id]),      'iconHide'],
		['Usuń',        $adminPanelURL('pages', ['deleteId' => $page->id]),    'iconDelete deleteConfirmAlert'],
	]; })
?>