<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');
header("Content-Type:text/html; charset=utf-8");

define("ROOT", dirname(__FILE__));
define("DEFAULT_URL", substr(ROOT, strlen($_SERVER['DOCUMENT_ROOT'])));

// Dispatcher File
require_once ROOT . '/library/Dispatcher.php';

// new Dispatcher;
Dispatcher::startup();
