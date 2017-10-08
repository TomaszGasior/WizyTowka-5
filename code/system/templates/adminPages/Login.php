<p class="screenReaders">Zaloguj się do panelu administracyjnego.</p>

<form method="post">
	<?= (new WizyTowka\HTMLFormFields)
		->text('Nazwa użytkownika', 'nofilter_name', $lastUsername, ['required'=>true, 'autofocus'=>true])
		->password('Hasło', 'nofilter_password', ['required'=>true])
	?>

	<button>Zaloguj się</button>
</form>