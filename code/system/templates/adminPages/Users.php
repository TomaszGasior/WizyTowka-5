<?= (new WizyTowka\HTMLElementsList('elementsList listView'))
	->collection($users)
	->title(function($user){ return $user->name; })
	->link(function($user){ return WizyTowka\AdminPanel::URL('userEdit', ['id' => $user->id]); })
	->menu(function($user){ return [
		['Edytuj', WizyTowka\AdminPanel::URL('userEdit', ['id' => $user->id]), 'iconEdit'],
		['Usuń', WizyTowka\AdminPanel::URL('users', ['deleteId' => $user->id]), 'iconDelete'],
	]; })
	->emptyMessage('<!--  -->')
?>