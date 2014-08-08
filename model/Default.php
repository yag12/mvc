<?php
class DefaultModel extends Model
{
	//protected $name = 'mysql';
	public function getUser()
	{
		$req = $this->where(array('info.userName' => '허짜장', 'istorage.objectData' => new MongoRegex('/UNIT/')))
					->fields(array('istorage.objectData'))
					->select('user');
		//$req = $this->where(array("GUSN < 10"))->select('WST_GT_CHAR');
		//$req = $this->where(array('info.userName' => 'yag12'))
		//			->fields(array('info'))
		//			//->limit(0, 2)
		//			//->sort(array('_id' => -1))
		//			->select('user', 'cacheUserDatas');

		//$co = $this->db->selectCollection("user");
		//$user = $co->find(array('info.userName'=>'yag12'), array('info'));
		//$req = null;
		//foreach($user as $doc)
		//{
		//	$req[] = $doc;
		//}

		return $req;
	}
}
