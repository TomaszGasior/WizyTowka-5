<form method="post">
	<h3>Zmiana hasła logowania</h3>

	<?= (new HTMLFormFields)
		->password('Aktualne hasło', 'currentPassword')
		->password('Nowe hasło', 'passwordText_1')
		->password('Ponownie nowe hasło', 'passwordText_2')
	?>

	<button>Zmień hasło</button>
</form>