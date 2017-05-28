<p class="screenReaders">Zaloguj się do panelu administracyjnego.</p>
<form method="post">
	<?= (new WizyTowka\HTMLFormFields)
		->text('Nazwa użytkownika', 'name', '', ['required'=>true])
		->password('Hasło', 'password', ['required'=>true])
	?>
	<button>Zapisz</button>
</form>