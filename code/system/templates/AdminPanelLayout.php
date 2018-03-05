<!doctype html>

<html lang="pl">

<head>
	<?= $head ?>
</head>

<body id="<?= $id ?>">
	<div class="adminPanel">
		<a class="a11yJumpLink" href="#main">Przeskocz do bloku głównego</a>
		<a class="a11yJumpLink" href="#navigation">Przeskocz do menu</a>

		<header id="header" class="columns">
			<h1>WizyTówka — panel administracyjny</h1>
			<?= $topMenu ?>
			<a class="mobileMenuOpen" href="#navigation" aria-hidden="true">Otwórz menu</a>
		</header>

		<div class="columns">
			<nav id="navigation" tabindex="-1">
				<h2 class="screenReaders">Menu nawigacyjne</h2>
				<?= $mainMenu ?>
				<a class="mobileMenuClose" href="#" aria-hidden="true">Zamknij menu</a>
			</nav>

			<main id="main" tabindex="-1">
				<?= $message ?>

				<header class="columns">
					<h2><?= $pageTitle ?></h2>
					<?= $contextMenu ?>
				</header>

				<?= $pageTemplate ?>
			</main>
		</div>
	</div>

	<script>
	(function(){
		// Change URL hash manually by script below and don't mess up browser history.
		// Native behavior of HTML anchor is kept for users without JavaScript.
		document.querySelectorAll('a.mobileMenuOpen, a.mobileMenuClose').forEach(function(element){
			element.addEventListener('click', function(event){
				location.replace(this.href);
				event.preventDefault();
			});
		});

		// Confirmation alert for delete operations.
		document.querySelectorAll('.deleteConfirmAlert').forEach(function(element){
			element.addEventListener('click', function(event){
				if (!confirm('Czy na pewno usunąć? Tej czynności nie można cofnąć.')) {
					event.preventDefault();
				}
			});
		})
	})();
	</script>
</body>

</html>