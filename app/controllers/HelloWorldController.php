<?php
/**
 * li₃: the most RAD framework for PHP (http://li3.me)
 *
 * Copyright 2016, Union of RAD. All rights reserved. This source
 * code is distributed under the terms of the BSD 3-Clause License.
 * The full license text can be found in the LICENSE.txt file.
 */

namespace app\controllers;

class HelloWorldController extends \lithium\action\Controller {

	public function index() {
		return $this->render(['layout' => false]);
	}

	public function to_string() {
		return "Hello World";
	}

	public function to_json() {
		return $this->render(['json' => 'Hello World']);
	}
}

?>