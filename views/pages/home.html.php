<?php
/**
 * li₃: the most RAD framework for PHP (http://li3.me)
 *
 * Copyright 2009, Union of RAD. All rights reserved. This source
 * code is distributed under the terms of the BSD 3-Clause License.
 * The full license text can be found in the LICENSE.txt file.
 */

use lithium\core\Libraries;
use lithium\core\Environment;
use lithium\data\Connections;

$this->title('Home');
$this->html->style('debug', ['inline' => false]);

$notify = function($status, $message, $solution = null) {
	$html  = "<h4 class=\"alert alert-{$status}\">{$message}</h4>";
	$html .= "<p>{$solution}</p>";
	return $html;
};

$docUrl = function($class) {
	return 'http://li3.me/docs/api/lithium/1.2.x/lithium/' . str_replace('\\', '/', $class);
};

$support = function($heading, $data) use ($docUrl) {
	$result = "<h3>{$heading}</h3>";

	if (is_string($data)) {
		return $result . $data;
	}
	$result .= '<ul class="lithium-indicator">';

	foreach ($data as $class => $enabled) {
		$name = substr($class, strrpos($class, '\\') + 1);
		$url = $docUrl($class);
		$class = $enabled ? 'enabled' : 'disabled';
		$title = $enabled ? "Adapter `{$name}` is enabled." : "Adapter `{$name}` is disabled.";
		$result .= "<li><a href=\"{$url}\" title=\"{$title}\" class=\"{$class}\">{$name}</a></li>";
	}
	$result .= '</ul>';

	return $result;
};

$compiled = function($flag) {
	ob_start();
	phpinfo(INFO_GENERAL);
	return strpos(ob_get_clean(), $flag) !== false;
};

$checks = [
	'resourcesWritable' => function() use ($notify) {
		if (is_writable($path = Libraries::get(true, 'resources'))) {
			return $notify('success', 'Resources directory is writable');
		}
		$app = basename(LITHIUM_APP_PATH);
		$path = str_replace(LITHIUM_APP_PATH . '/', null, $path);
		$solution = null;

		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			$solution  = 'To fix this, run the following from the command line: ';
			$solution .= "<pre><code>";
			$solution .= !empty($app) ? "$ cd {$app}\n" : null;
			$solution .= "$ chmod -R 0777 {$path}";
			$solution .= "</code></pre>";
		} else {
			$path = realpath($path);
			$solution  = 'To fix this, give <code>modify</code> rights to the user ';
			$solution .= "<code>Everyone</code> on directory <code>{$path}</code>.";
		}
		return $notify(
			'fail',
			'Your resource path is not writeable',
			$solution
		);
	},
	'errorReporting' => function() use ($notify) {
		if (error_reporting() & E_STRICT) {
			return;
		}
		return $notify(
			'warning',
			'Reporting of strict errors is disabled',
			'Spotting strict errors early during development helps making your code better. ' .
			'Please set <code>error_reporting = E_ALL</code> in your <code>php.ini</code> settings.'
		);
	},
	'mbstringFuncOverload' => function() use ($notify) {
		if (!ini_get('mbstring.func_overload')) {
			return;
		}
		return $notify(
			'error',
			'Multibyte String function overlading is enabled in your PHP configuration',
			'Please set <code>mbstring.func_overload = 0</code>
			in your <code>php.ini</code> settings.'
		);
	},
	'curlwrappers' => function() use ($notify, $compiled) {
		if (!$compiled('with-curlwrappers')) {
			return;
		}
		return $notify(
			'error',
			'Curlwrappers are enabled, some things might not work as expected.',
			"This is an expiremental and usually broken feature of PHP.
			Please recompile your PHP binary without using the <code>--with-curlwrappers</code>
			flag or use a precompiled binary that was compiled without the flag."
		);
	},
	'shortOpenTag' => function() use ($notify, $compiled) {
		if (!ini_get('short_open_tag')) {
			return;
		}
		return $notify(
			'warning',
			'Short open tags are enabled, you may want to disable them.',
			"It is recommended to not rely on this option being enabled.
			To increase the portability of your code disable this option by setting
			<code>short_open_tag = Off</code> in your <code>php.ini</code>."
		);
	},
	'database' => function() use ($notify) {
		if ($config = Connections::config()) {
			return $notify('success', 'Database connection(s) configured');
		}
		return $notify(
			'warning',
			'No database connection defined',
			"To create a database connection:
			<ol>
				<li>Edit the file <code>config/bootstrap.php</code>.</li>
				<li>
					Uncomment the line having
					<code>require __DIR__ . '/bootstrap/connections.php';</code>.
				</li>
				<li>Edit the file <code>config/bootstrap/connections.php</code>.</li>
			</ol>"
		);
	},
	'change' => function() use ($notify, $docUrl) {
		$template = $this->html->link('template', $docUrl('lithium\template'));

		return $notify(
			'warning',
			"You're using the application's default home page",
			"To change this {$template}, edit the file
			<code>views/pages/home.html.php</code>.
			To change the layout,
			(that is what's wrapping content)
			edit the file <code>views/layouts/default.html.php</code>."
		);
	},
	'dbSupport' => function() use ($support) {
		$paths = ['data.source', 'adapter.data.source.database', 'adapter.data.source.http'];
		$map = [];

		error_reporting(($original = error_reporting()) & ~E_USER_DEPRECATED);
		foreach ($paths as $path) {
			$list = Libraries::locate($path, null, ['recursive' => false]);

			foreach ($list as $class) {
				if (method_exists($class, 'enabled')) {
					$map[$class] = $class::enabled();
				}
			}
		}
		error_reporting($original);
		return $support('Database support', $map);
	},
	'cacheSupport' => function() use ($support) {
		$list = Libraries::locate('adapter.storage.cache', null, ['recursive' => false]);
		$map = [];

		error_reporting(($original = error_reporting()) & ~E_USER_DEPRECATED);
		foreach ($list as $class) {
			if (method_exists($class, 'enabled')) {
				$map[$class] = $class::enabled();
			}
		}
		error_reporting($original);
		return $support('Cache support', $map);
	},
	'routing' => function() use ($support, $docUrl) {
		$routing = $this->html->link('routing', $docUrl('lithium\net\http\Router'));

		return $support(
			'Custom routing',
			"Routes allow you to map custom URLs to your application code. To change the
			{$routing}, edit the file <code>config/routes.php</code>."
		);
	},
	'tests' => function() use ($notify, $support, $docUrl) {
		if (Environment::is('production')) {
			$docsLink = $this->html->link(
				'the documentation',
				$docUrl('lithium\core\Environment::is()')
			);

			return $notify(
				'error',
				"Can't run tests",
				"<p>li₃'s default environment detection rules have determined that you are
				running in production mode. Therefore, you will not be able to run tests from the
				web interface. You can do any of the following to remedy this:</p>
				<ul>
					<li>Run this application locally</li>
					<li>Run tests from the console, using the <code>li3 test</code> command</li>
					<li>
						Implementing custom environment detection rules;
						see {$docsLink} for examples
					</li>
				</ul>"
			);
		}
		$tests = $this->html->link('run all tests', [
			'controller' => 'lithium\test\Controller',
			'args' => 'all'
		]);
		$dashboard = $this->html->link('test dashboard', [
			'controller' => 'lithium\test\Controller'
		]);
		$ticket = $this->html->link(
			'file a ticket', 'https://github.com/UnionOfRAD/lithium/issues'
		);

		return $support(
			'Run the tests',
			"Check the {$dashboard} or {$tests} now to ensure li₃ is working as expected."
		);
	}
];

?>
<div class="jumbotron">
	<h1><?=ucwords(basename(LITHIUM_APP_PATH))?></h1>
	<h2>
		Powered by <a href="http://li3.me/">li₃</a>.
	</h2>
</div>

<hr>

<h3>Current Setup</h3>
<?php foreach ($checks as $check): ?>
	<?php echo $check(); ?>
<?php endforeach; ?>

<h3>Quickstart</h3>
<p>
	<?php echo $this->html->link(
		'Quickstart', 'http://li3.me/docs/manual/quickstart'
	); ?> is a guide for PHP users who are looking to start building a simple application.
</p>

<h3>Learn more</h3>
<p>
	Read the
	<?php echo $this->html->link('Manual', 'http://li3.me/docs/book/manual/1.x'); ?>
	for detailed explanations and tutorials. The
	<?php echo $this->html->link('API documentation', 'https://li3.me/docs/api/lithium/1.2.x/lithium'); ?>
	has all the implementation details you've been looking for.
</p>

<h3>Community</h3>
<p>
	li3 is not just a framework, but the embodiment of a community. This community is dedicated to open collaboration and friendly discourse, with the goal of producing better quality software.
	Most importantly, you are invited to <em>participate</em>.
</p>
<p>
	For <strong>general support</strong> have a look on the questions tagged with <em>lithium</em>
	<?php echo $this->html->link('on stackoverflow', 'http://stackoverflow.com/questions/tagged/lithium') ?>.
</p>
