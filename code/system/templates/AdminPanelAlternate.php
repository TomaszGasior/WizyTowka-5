<!doctype html>

<html lang="pl">

<head>
	<?= $head ?>
</head>

<body>
	<div class="adminPanelAlternative">
		<header id="header">
			<h1>WizyTówka — panel administracyjny</h1>
		</header>

		<main id="main">
			<?= $message ?>

			<header>
				<h2><?= $pageTitle ?></h2>
			</header>

			<?= $pageTemplate ?>
		</main>
	</div>
</body>

</html>