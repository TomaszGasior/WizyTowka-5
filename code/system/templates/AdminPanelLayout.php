<!doctype html>

<html lang="pl">

<head>
	<?= $head ?>
</head>

<body>
	<div class="adminPanel">
		<a class="a11yJumpLink" href="#navigation">Przeskocz do menu</a>
		<a class="a11yJumpLink" href="#main">Przeskocz do bloku głównego</a>

		<header id="header" class="columns">
			<h1>WizyTówka — panel administracyjny</h1>
			<ul>
				<li class="iUser"><a href="#">tomaszgasior</a></li>
				<li class="iUpdates"><a href="#">Zaktualizuj</a></li>
				<li class="iWebsite"><a href="#">Zobacz witrynę</a></li>
				<li class="iLogout"><a href="#">Wyloguj się</a></li>
			</ul>
		</header>

		<div class="columns">
			<nav id="navigation" tabindex="-1">
				<h2 class="screenReaders">Menu nawigacyjne</h2>
				<ul>
					<li class="iPages"><a href="<?= WizyTowka\AdminPanel::URL('pages') ?>">Strony</a></li>
					<li class="iAdd"><a href="#">Utwórz stronę</a></li>
					<li class="iDrafts"><a href="<?= WizyTowka\AdminPanel::URL('drafts') ?>">Szkice</a></li>
					<li class="iAdd"><a href="#">Utwórz szkic</a></li>
					<li class="iFiles"><a href="#">Pliki</a></li>
					<li class="iAdd"><a href="#">Wyślij pliki</a></li>
					<li class="iUsers"><a href="#">Użytkownicy</a></li>
					<li class="iAdd"><a href="#">Utwórz użytkownika</a></li>
					<li class="iMenus"><a href="#">Menu</a></li>
					<li class="iWidgets"><a href="#">Obszary</a></li>
					<li class="iSettings"><a href="#">Ustawienia</a></li>
					<li class="iCustomization"><a href="#">Personalizacja</a></li>
					<li class="iFilesEditor"><a href="#">Edytor plików</a></li>
					<li class="iBackup"><a href="#">Kopia zapasowa</a></li>
					<li class="iInformations"><a href="#">Informacje</a></li>
				</ul>
			</nav>

			<main id="main" tabindex="-1">
				<?php if ($message) { ?>
					<div role="alert" <?= ($messageError ? 'class="error"' : '') ?> id="message"><?= $message ?></div>
				<?php } ?>

				<header class="columns">
					<h2><?= $pageTitle ?></h2>
					<ul>
						<li><a href="#">Zawartość</a></li>
						<li><a href="#">Ustawienia</a></li>
					</ul>
				</header>

				<?= $pageTemplate ?>
			</main>
		</div>

	</div>
</body>

</html>