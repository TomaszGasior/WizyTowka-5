<?= (new HTMLElementsList('elementsList'))
	->collection($users)
	->title(function($user){ return $user->name; })
	->menu(function($user){ return [
		['Edytuj', AdminPanel::URL('userEdit', ['id' => $user->id]),    'iconEdit'],
		['Usuń',   AdminPanel::URL('users', ['deleteId' => $user->id]), 'iconDelete deleteConfirmAlert'],
	]; })
?>