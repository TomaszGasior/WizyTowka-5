<!doctype html>

<html lang="<?= $lang ?>">
	<head>
		<?= $head ?>
	</head>

	<body>
		<header>
			<?= $websiteHeader ?>
		</header>

		<nav>
			<?= $menu(1) ?>
		</nav>

		<main>
			<header>
				<?= $pageHeader ?>
			</header>
			<?= $pageContent ?>
		</main>

		<footer>
			<?= $websiteFooter ?>
		</footer>
	</body>
</html>