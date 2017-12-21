<form method="post">
	<h3>Dane użytkownika</h3>

	<?php if ($createInsteadEdit) { ?>
		<?= (new HTMLFormFields)
			->text('Nazwa użytkownika', 'name', '',
				['required'=>true, 'pattern'=>'[a-zA-Z0-9_\-.]*', 'title'=>'Dozwolone znaki: litery, cyfry, minus, kropka, podkreślnik.']
			)
		?>
	<?php } else { ?>
		<?= (new HTMLFormFields)
			->text('Nazwa użytkownika', 'name', $user->name,
				['pattern'=>'[a-zA-Z0-9_\-.]*', 'title'=>'Dozwolone znaki: litery, cyfry, minus, kropka, podkreślnik.']
			)
			->text('Data utworzenia', 'createdTime', $user->createdTime, ['disabled'=>true])
		?>
	<?php } ?>

	<p class="information">W nazwie użytkownika dozwolone są wyłącznie litery (bez polskich znaków diakrytycznych), cyfry, minus, kropka i podkreślnik. Wielkość liter ma znaczenie.</p>

	<h3>Hasło</h3>

	<?= (new HTMLFormFields)
		->password('Hasło', 'nofilter_passwordText_1', ['required'=>$createInsteadEdit])
		->password('Ponownie hasło', 'nofilter_passwordText_2', ['required'=>$createInsteadEdit])
	?>

	<?php if (!$createInsteadEdit) { ?>
		<p class="information">Pozostaw powyższe pola puste, by nie zmieniać hasła.</p>
	<?php } ?>

	<h3>Uprawnienia</h3>

	<?= (new HTMLFormFields)
		->checkbox('Tworzenie i edycja stron', 'permissions[CREATING_PAGES]', $permissions['CREATING_PAGES'])
		->checkbox('Wysyłanie plików', 'permissions[SENDING_FILES]', $permissions['SENDING_FILES'])
		->checkbox('Edycja stron i plików innych użytkowników', 'permissions[EDITING_OTHERS_PAGES]', $permissions['EDITING_OTHERS_PAGES'])
		->checkbox('Modyfikacja elementów witryny (obszary, menu)', 'permissions[EDITING_SITE_ELEMENTS]', $permissions['EDITING_SITE_ELEMENTS'])
		->checkbox('Modyfikacja konfiguracji witryny (ustawienia, personalizacja)', 'permissions[EDITING_SITE_CONFIG]', $permissions['EDITING_SITE_CONFIG'])
		->checkbox('<b>Super użytkownik</b>: zarządzanie użytkownikami i dostęp do edytora plików', 'permissions[SUPER_USER]', $permissions['SUPER_USER'])
	?>
	<button>Zapisz zmiany</button>
</form>