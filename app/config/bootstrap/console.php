<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2016, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\aop\Filters;
use lithium\console\Dispatcher;
use lithium\core\Environment;
use lithium\core\Libraries;

/**
 * This filter sets the environment based on the current request. By default, `$request->env`, for
 * example in the command `li3 help --env=production`, is used to determine the environment.
 *
 * Routes are also loaded, to facilitate URL generation from within the console environment.
 *
 */
Filters::apply(Dispatcher::class, 'run', function($params, $next) {
	Environment::set($params['request']);

	foreach (array_reverse(Libraries::get()) as $name => $config) {
		if ($name === 'lithium') {
			continue;
		}
		$file = "{$config['path']}/config/routes.php";
		file_exists($file) ? call_user_func(function () use ($file) { include $file; }) : null;
	}
	return $next($params);
});

/**
 * This filter will convert {:heading} to the specified color codes. This is useful for colorizing
 * output and creating different sections.
 *
 */
// Filters::apply(Dispatcher::class, '_call', function($params, $next) {
// 	$params['callable']->response->styles([
// 		'heading' => '\033[1;30;46m'
// 	]);
// 	return $next($params);
// });

?>