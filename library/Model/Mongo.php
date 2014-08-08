<?php
class MongoModel implements InterfaceModel
{
	/**
	* @Desc Database
	* @Var Object
	*/
	private $db = null;

	/**
	* @Desc Where
	* @Var array
	*/
	public $where = array();

	/**
	* @Desc Fields
	* @Var array
	*/
	public $fields = array();

	/**
	* @Desc Limit
	* @Var array or null
	*/
	public $limit = null;

	/**
	* @Desc Sort
	* @Var array or null
	*/
	public $sort = null;

	/**
	* @Desc Construct
	* @Param Database $db
	* @Return void
	*/
	public function __construct(&$db = null)
	{
		$this->db = &$db;
	}

	/**
	* @Desc Data Select
	* @Param mixed $tb
	* @Return array
	*/
	public function select($tb = null)
	{
		$rows = null;
		$collected = $this->db->selectCollection($tb);
		$result = $collected->find($this->where, $this->fields);

		if(!empty($this->limit))
		{
			$result = $result->limit($this->limit['limit'])
							->skip($this->limit['offset']);
		}

		$this->total = $result->count();

		if(!empty($this->sort))
		{
			$result = $result->sort($this->sort);
		}

		if(!empty($result))
		{
			foreach($result as $row)
			{
				$rows[] = $row;
			}
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
		$collected = $this->db->selectCollection($tb);
		$collected->insert($this->fields);

		return true;
	}

	/**
	* @Desc Data Update
	* @Param mixed $tb
	* @Return boolean
	*/
	public function update($tb = null)
	{
		$collected = $this->db->selectCollection($tb);
		$collected->update($this->where, $this->fields, array('upsert' => true));

		return true;
	}

	/**
	* @Desc Data Delete
	* @Param mixed $tb
	* @Return boolean
	*/
	public function remove($tb = null)
	{
		$collected = $this->db->selectCollection($tb);
		$collected->remove($this->where);

		return true;
	}

	/**
	* @Desc Reset
	* @Param void
	* @Return void
	*/
	public function reset()
	{
		$this->where = array();
		$this->fields = array();
		$this->limit = null;
		$this->sort = null;
	}
}
