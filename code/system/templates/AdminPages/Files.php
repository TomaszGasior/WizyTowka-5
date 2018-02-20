<?= (new HTMLElementsList('elementsList listView'))
	->collection($files)
	->title(function($file){ return HTML::escape($file->getName()); })
	->link(function($file){ return AdminPanel::URL('fileEdit', ['name' => $file->getName()]); })
	->menu(function($file){ return [
		['Edytuj', AdminPanel::URL('fileEdit', ['name' => $file->getName()]), 'iconEdit'],
		['Usuń', AdminPanel::URL('files', ['deleteName' => $file->getName()]), 'iconDelete'],
	]; })
	->emptyMessage('Nie wysłano jeszcze żadnych plików. Tutaj zazwyczaj znajdują się pliki dodane jako załączniki do stron czy galerii zdjęć.')
?>