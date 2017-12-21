<?= (new HTMLElementsList('elementsList listView'))
	->collection($users)
	->title(function($user){ return $user->name; })
	->link(function($user){ return AdminPanel::URL('userEdit', ['id' => $user->id]); })
	->menu(function($user){ return [
		['Edytuj', AdminPanel::URL('userEdit', ['id' => $user->id]), 'iconEdit'],
		['Usuń', AdminPanel::URL('users', ['deleteId' => $user->id]), 'iconDelete'],
	]; })
?>