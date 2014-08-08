<?php
class PhotoModel extends Model
{
	public function insertPhoto($uid = 0, $model = 0, $file_name = null)
	{
		$this->fields(array(
				'uid' => $uid,
				'model' => $model,
				'photo_name' => $file_name,
				'regdate' => mktime(),
			))->insert('webapp_photo');

		return true;
	}

	public function getPhoto($limit, $offset)
	{
		$rows = $this->limit($limit, $offset)->select('webapp_photo');
		return $rows;
	}
}
