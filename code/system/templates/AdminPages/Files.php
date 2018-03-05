<?= (new HTMLElementsList('elementsList'))
	->collection($files)
	->title(function($file){ return HTML::escape($file->name) . ' (' . HTML::formatFileSize($file->size) . ')'; })
	->link(function($file){ return urlencode($file->url); })
	->menu(function($file){ return [
		['Zmień nazwę', AdminPanel::URL('fileEdit', ['name' => $file->name]),    'iconEdit'],
		['Usuń',        AdminPanel::URL('files', ['deleteName' => $file->name]), 'iconDelete deleteConfirmAlert'],
	]; })
	->emptyMessage('Nie wysłano jeszcze żadnych plików. Tutaj zazwyczaj znajdują się pliki dodane jako załączniki do stron czy galerii zdjęć.')
?>