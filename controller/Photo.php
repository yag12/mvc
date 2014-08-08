<?php
class PhotoController extends Controller
{
	public function index()
	{
		$offset = 10;
		$pgnum = !empty($this->params['_GET']['pgnum']) ? $this->params['_GET']['pgnum'] : 1;
		$limit = ($pgnum - 1) * $offset;

		$model = $this->db->getModel('Photo');
		$rows = $model->getPhoto($limit, $offset);
		$paginator = $model->getPaginator($pgnum);

		$this->setParam('paginator', $paginator);
		$this->setParam('rows', $rows);
		$this->render('index.php');
	}
}
