<!doctype html>

<html lang="pl">

<head>
	<?= $head ?>
</head>

<body>
	<div class="adminPanel">
		<a class="a11yJumpLink" href="#main">Przeskocz do bloku głównego</a>
		<a class="a11yJumpLink" href="#navigation">Przeskocz do menu</a>

		<header id="header" class="columns">
			<h1>WizyTówka — panel administracyjny</h1>
			<?= $topMenu ?>
		</header>

		<div class="columns">
			<nav id="navigation" tabindex="-1">
				<h2 class="screenReaders">Menu nawigacyjne</h2>
				<?= $mainMenu ?>
			</nav>

			<main id="main" tabindex="-1">
				<?php if ($message) { ?>
					<div role="alert" <?= $messageError ? 'class="error"' : '' ?> id="message"><?= $message ?></div>
				<?php } ?>

				<header class="columns">
					<h2><?= $pageTitle ?></h2>
					<?= $contextMenu ?>
				</header>

				<?= $pageTemplate ?>
			</main>
		</div>
	</div>
</body>

</html>