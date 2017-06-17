<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($files)
	->title(function($file){ return $file->title; })
	->link(function($file){ return WizyTowka\AdminPanel::URL('fileEdit', ['id' => $file->id]); })
	->menu(function($file){ return [
		['Edytuj', WizyTowka\AdminPanel::URL('fileEdit', ['id' => $file->id]), 'iEdit'],
		['Usuń', WizyTowka\AdminPanel::URL('files', ['deleteId' => $file->id]), 'iDelete'],
	]; })
	->emptyMessage('Nie wysłano jeszcze żadnych plików.')
?>