<div class="systemVersion">
	<p><?= $versionName ?></p>
	<p>Ta witryna działa w oparciu o system zarządzania treścią WizyTówka.</p>
	<p>Używając systemu WizyTówka, akceptujesz jego licencję.</p>
	<p>Data wydania tej wersji systemu: <?= $utils::formatDate($releaseDate) ?>.</p>
	<p><a href="https://wizytowka.tomaszgasior.pl" target="_blank">https://wizytowka.tomaszgasior.pl</a></p>
</div>

<?php if ($betaVersionWarning) { ?>
	<p class="warning">Używana wersja systemu jest wersją testową, może zawierać błędy. Nie należy używać jej do celów produkcyjnych, budować przy jej pomocy dostępnych publicznie witryn.</p>
<?php } ?>