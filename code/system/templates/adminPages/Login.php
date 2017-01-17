<!doctype html>

<html lang="pl">

<head>
	<?= $head ?>
</head>

<body>
	<div class="loginForm">
		<header id="header">
			<h1>WizyTówka — panel administracyjny</h1>
		</header>

		<main id="main">
			<?php if ($message) { ?>
				<div role="alert" <?= $messageError ? 'class="error"' : '' ?> id="message"><?= $message ?></div>
			<?php } ?>

			<header>
				<h2>Panel administracyjny<span class="screenReaders"> — logowanie </span></h2>
			</header>

			<form method="post">
				<?= (new WizyTowka\HTMLFormFields)
					->text('Nazwa użytkownika', 'name', '', ['required'=>true])
					->password('Hasło', 'password', ['required'=>true])
				?>
				<button>Zapisz</button>
			</form>
		</main>
	</div>
</body>

</html>