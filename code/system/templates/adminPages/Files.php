<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($files)
	->title(function($file){ return $file->title; })
	->link(function($file){ return WizyTowka\AdminPanel::URL('fileEdit', ['id' => $file->id]); })
	->menu(function($file){ return [
		['Edytuj', WizyTowka\AdminPanel::URL('fileEdit', ['id' => $file->id]), 'iconEdit'],
		['Usuń', WizyTowka\AdminPanel::URL('files', ['deleteId' => $file->id]), 'iconDelete'],
	]; })
	->emptyMessage('Nie wysłano jeszcze żadnych plików. Tutaj zazwyczaj znajdują się pliki dodane jako załączniki do stron czy galerii zdjęć.')
?>