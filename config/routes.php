<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
	'Bakkerij/Notifier',
	['path' => '/notifications'],
	function (RouteBuilder $routes) {
		$routes->connect('/', ['controller' => 'Notifications']);
		$routes->fallbacks();
	}
);
