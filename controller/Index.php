<?php
class IndexController extends Controller
{
	public function index()
	{
		$this->render('index.php');
	}

	public function test()
	{
		return $this->params['test'];
	}
}
