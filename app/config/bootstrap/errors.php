<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2016, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\action\Dispatcher;
use lithium\core\ErrorHandler;
use lithium\action\Response;
use lithium\net\http\Media;

ErrorHandler::apply(Dispatcher::class . '::run', [], function($info, $params) {
	$response = new Response([
		'request' => $params['request'],
		'status' => $info['exception']->getCode()
	]);

	Media::render($response, compact('info', 'params'), [
		'library' => true,
		'controller' => '_errors',
		'template' => 'development',
		'layout' => 'error',
		'request' => $params['request']
	]);
	return $response;
});

?>