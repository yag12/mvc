<?php
/****************************************************************************
* TDD
* http://code.tutsplus.com/tutorials/lets-tdd-a-simple-app-in-php--net-26186
*
* #] phpunit index.php
*****************************************************************************/
define('ROOT', dirname(dirname(__FILE__)));
require_once ROOT . '/library/Dispatcher.php';

class IndexTest extends PHPUnit_Framework_TestCase {
	public function setUp()
	{
		Dispatcher::setRequire();
	}

	public function testIndex() {
		$this->assertEquals(1, Dispatcher::initContoller('Index', 'test', 'html', array('test'=>1)));
		$this->assertEquals(1, Dispatcher::initContoller('Index', 'test', 'html', array('test'=>2)));
	}
}
