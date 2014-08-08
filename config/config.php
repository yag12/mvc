<?php
$config = array(
	'database' => array(
		'mysql' => array(
			'type' => 'mysqli',
			'host' => '127.0.0.1',
			'port' => 3306,
			'db' => 'test',
			'user' => 'root',
			'passwd' => '1',
		)
	),
	'memcached' => array(
		'servers' => array(               
			array('127.0.0.1', 11211, 100),
		),                                
		'prefix_key' => 'prefix_key'
	),
	'auth' => array(
		'controller' => 'Auth',
		'action' => 'index'
	),
);
