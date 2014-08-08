<?php
abstract class Model
{
	/**
	* @Desc Database
	* @Var Object
	*/
	protected $db = null;

	/**
	* @Desc Memcached
	* @Var mixed
	*/
	protected $cache = null;

	/**
	* @Desc Database Name
	* @Var mixed
	*/
	protected $name = 'default';

	/**
	* @Desc Total Row
	* @Var int
	*/
	protected $total = 0;
	protected $limit = 10;
	protected $count = 5;

	/**
	* @Desc Database Type
	* @Var mixed
	*/
	private $type = null;

	/**
	* @Desc Model Class
	* @Var Model
	*/
	private $model = null;

	/**
	* @Desc Construct
	* @Param Database $db
	* @Return void
	*/
	public function __construct(&$db = null)
	{
		$dbName = $this->name;
		if(!empty($db->$dbName))
		{
			$this->db = &$db->$dbName;
		}

		if(!empty($db->cache))
		{
			$this->cache = &$db->cache;
		}

		if(!empty($db->config))
		{
			$this->setModel($db->config);
		}
	}

	/**
	* @Desc Database Type
	* @Param array $config
	* @Return void
	*/
	private function setModel($config = array())
	{
		if($this->name == 'default' || empty($this->name))
		{
			$current= current($config);
			$this->type = $current['type'];
		}
		else
		{
			$this->type = $config[$this->name]['type'];
		}

		$modelName = $this->type . 'Model';
		if(class_exists($modelName))
		{
			$this->model = new $modelName($this->db);
		}
	}

	/**
	* @Desc set Cache
	* @Param mixed $key
	* @Param mixed $value
	* @Return boolean
	*/
	protected function setCache($key = null, $value = null)
	{
		if(!empty($this->cache))
		{
			$this->cache->set($key, $value);
		}

		return true;
	}

	/**
	* @Desc get Cache
	* @Param mixed $key
	* @Return mixed
	*/
	protected function getCache($key = null)
	{
		if(!empty($this->cache))
		{
			return $this->cache->get($key);
		}

		return null;
	}

	/**
	* @Desc Data Select
	* @Param mixed $tb
	* @Param false or string $cache
	* @Return array
	*/
	public function select($tb = null, $cache = false)
	{
		$rows = null;
		// 캐시에 저장된 데이터 불러오기
		if($cache !== false && is_string($cache))
		{
			$rows = $this->getCache($cache);
			$this->total = $this->getCache($cache . '_total');

			if(!empty($rows))
			{
				$this->model->reset();
				return $rows;
			}
		}

		$rows = $this->model->select($tb);
		$this->total = $this->model->total;
		$this->model->reset();

		// 캐시에 데이터 저장
		if($cache !== false && is_string($cache))
		{
			$this->setCache($cache, $rows);
			$this->setCache($cache . '_total', $this->total);
		}

		return $rows;
	}

	/**
	* @Desc Data Insert
	* @Param mixed $tb
	* @Return boolean
	*/
	public function insert($tb = null)
	{
		$this->model->insert($tb);
		$this->model->reset();

		return true;
	}

	/**
	* @Desc Data Update
	* @Param mixed $tb
	* @Param false or string $cache
	* @Return boolean
	*/
	public function update($tb = null, $cache = false)
	{
		$this->model->update($tb);
		$this->model->reset();

		// 저장된 캐시 삭제
		if($cache !== false && is_string($cache))
		{
			$this->cache->del($cache);
		}

		return true;
	}

	/**
	* @Desc Data Delete
	* @Param mixed $tb
	* @Param false or string $cache
	* @Return boolean
	*/
	public function remove($tb = null, $cache = false)
	{
		$this->model->remove($tb);
		$this->model->reset();

		// 저장된 캐시 삭제
		if($cache !== false && is_string($cache))
		{
			$this->cache->del($cache);
		}

		return true;
	}

	/**
	* @Desc Data Where
	* @Param array $data
	* @Return Model
	*/
	public function where($data = array())
	{
		$this->model->where = $data;

		return $this;
	}

	/**
	* @Desc Data Limit
	* @Param int $offset
	* @Param int $limit
	* @Return Model
	*/
	public function limit($offset = 0, $limit = 0)
	{
		$this->limit = $limit;
		$this->model->limit = array(
			'limit' => $limit, 
			'offset' => $offset
		);

		return $this;
	}

	/**
	* @Desc Select Data Field
	* @Param array $data
	* @Return Model
	*/
	public function fields($data = array())
	{
		$this->model->fields = $data;

		return $this;
	}

	/**
	* @Desc Select Data Sort
	* @Param array $data
	* @Return Model
	*/
	public function sort($data = array())
	{
		$this->model->sort = $data;

		return  $this;
	}

	/**
	* @Desc Paginator
	* @Param int $pgnum
	* @Param int $count
	* @Return array
	*/
	public function getPaginator($pgnum = 1, $count = 0)
	{
		if(empty($count)) $count = $this->count;
		$limit = $this->limit;
		$total = $this->total;

		$paginator = Func::getPaginator($total, $limit, $pgnum, $count);
		return array(
			'total' => $total,
			'pgnum' => $pgnum,
			'totalpg' => ceil($total / $limit),
			'paginator' => $paginator
		);
	}
}

interface InterfaceModel
{
	public function select($tb = null);
	public function insert($tb = null);
	public function update($tb = null);
	public function remove($tb = null);
	public function reset();
}
