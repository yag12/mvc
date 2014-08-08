<?php
class ModelController extends Controller
{
	public function index()
	{
		$this->render('index.php');
	}

	public function upload()
	{
		$uid = $this->params['_POST']['uid'];
		$model = $this->params['_POST']['model'];

		$uploadPath = dirname(dirname(__FILE__)) . '/image/photo';
		$upload = new FileUpload($uploadPath, array('jpg', 'gif', 'png'));
		$upload->upload();
		$file = $upload->getFiles();

		if(!empty($file[0]['name']))
		{
			$fileName = $file[0]['name'];
			$ext = end(explode(".", $fileName));
			$name = (microtime(true) * 10000) . "." . $ext;
			if(!rename($uploadPath . '/' . $fileName, $uploadPath . '/' . $name))
			{
				$name = $fileName;
			}

			$model = $this->db->getModel('Photo');
			$model->insertPhoto($uid, $model, $name);
		}

		header('Location:' . $this->url(array('action' => 'index')));
	}
}
