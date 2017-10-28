<h2><?= $pageTitle ?></h2>

<?php if ($properties) { ?>
	<ul>
		<?php foreach ($properties as $item) { ?>
			<li><?= $item ?></li>
		<?php } ?>
	</ul>
<?php } ?>