<?= (new HTMLElementsList('elementsList listView'))
	->collection($files)
	->title(function($file){ return $file->title; })
	->link(function($file){ return AdminPanel::URL('fileEdit', ['id' => $file->id]); })
	->menu(function($file){ return [
		['Edytuj', AdminPanel::URL('fileEdit', ['id' => $file->id]), 'iconEdit'],
		['Usuń', AdminPanel::URL('files', ['deleteId' => $file->id]), 'iconDelete'],
	]; })
	->emptyMessage('Nie wysłano jeszcze żadnych plików. Tutaj zazwyczaj znajdują się pliki dodane jako załączniki do stron czy galerii zdjęć.')
?>