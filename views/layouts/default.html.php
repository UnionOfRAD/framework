<?php
/**
 * li₃: the most RAD framework for PHP (http://li3.me)
 *
 * Copyright 2009, Union of RAD. All rights reserved. This source
 * code is distributed under the terms of the BSD 3-Clause License.
 * The full license text can be found in the LICENSE.txt file.
 */
?>
<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Application &gt; <?php echo $this->title(); ?></title>
	<?php echo $this->html->style(['bootstrap.min', 'lithified']); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->styles(); ?>
	<?php echo $this->html->link('Icon', null, ['type' => 'icon']); ?>
</head>
<body class="lithified">
	<div class="container-narrow">

		<div class="masthead">
			<ul class="nav nav-pills pull-right">
				<li>
					<a href="http://li3.me/docs/book/manual/1.x/quickstart">Quickstart</a>
				</li>
				<li>
					<a href="http://li3.me/docs/book/manual/1.x/">Manual</a>
				</li>
				<li>
					<a href="http://li3.me/docs/api/lithium/1.2.x/lithium">API</a>
				</li>
				<li>
					<a href="http://li3.me/">More</a>
				</li>
			</ul>
			<a href="http://li3.me/"><h3>&#10177;</h3></a>
		</div>

		<hr>

		<div class="content">
			<?php echo $this->content(); ?>
		</div>

		<hr>

		<div class="footer">
			<p>&copy; Union Of RAD <?php echo date('Y') ?></p>
		</div>

	</div>
</body>
</html>