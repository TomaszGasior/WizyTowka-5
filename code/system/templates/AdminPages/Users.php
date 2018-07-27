<?= (new $elementsList('elementsList'))
	->collection($users)
	->title(function($user){ return $user->name; })
	->menu(function($user) use ($adminPanelURL){ return [
		['Edytuj', $adminPanelURL('userEdit', ['id' => $user->id]),    'iconEdit'],
		['Usuń',   $adminPanelURL('users', ['deleteId' => $user->id]), 'iconDelete deleteConfirmAlert'],
	]; })
?>