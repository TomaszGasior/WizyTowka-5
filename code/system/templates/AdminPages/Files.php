<?= (new $elementsList('elementsList'))
	->collection($files)
	->title(function($file) use ($utils){ return $file->name . ' (' . $utils::formatFileSize($file->size) . ')'; })
	->link(function($file){ return urlencode($file->url); })
	->menu(function($file) use ($adminPanelURL){ return [
		['Zmień nazwę', $adminPanelURL('fileEdit', ['name' => $file->rawName]),    'iconEdit'],
		['Usuń',        $adminPanelURL('files', ['deleteName' => $file->rawName]), 'iconDelete deleteConfirmAlert'],
	]; })
	->emptyMessage('Nie wysłano jeszcze żadnych plików. Tutaj zazwyczaj znajdują się pliki dodane jako załączniki do stron czy galerii zdjęć.')
?>