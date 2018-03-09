<p class="screenReaders">Zaloguj się do panelu administracyjnego.</p>

<form method="post">
	<?= (new HTMLFormFields)
		->text('Nazwa użytkownika', 'name', $lastUsername, ['required' => true, 'autofocus' => true])
		->password('Hasło', 'password', ['required' => true])
	?>

	<button>Zaloguj się</button>
</form>