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
				<h2><?= $pageTitle ?></h2>
			</header>

			<?= $pageTemplate ?>
		</main>
	</div>
</body>

</html>