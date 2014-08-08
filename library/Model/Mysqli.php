<?php
class MysqliModel implements InterfaceModel
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
		$fields = !empty($this->fields) ? join(',', $this->fields) : '*';

		$wheres = null;
		if(!empty($this->where))
		{
			foreach($this->where as $key=>$value)
			{
				$wheres[] = $key . '=' . (is_string($value) ? "'" . $value . "'" : $value);
			}
			$wheres = join(' AND ', $wheres);
		}

		$sort = !empty($this->sort) ? ' ORDER BY ' . join(',', $this->sort) : '';
		$limit = null;

		if(!empty($this->limit['limit']))
		{
			$limit = ' LIMIT ' . $this->limit['limit'];
		}

		if(!empty($limit) && !empty($this->limit['offset']))
		{
			$limit = ' LIMIT ' . $this->limit['offset'] . ', ' . $this->limit['limit'];
		}

		$result = $this->db->query('SELECT COUNT(*) FROM ' . $tb . ' ' . $wheres);
		$count = !empty($result) ? $result->fetch_array(MYSQLI_NUM) : 0;
		$this->total = !empty($count[0]) ? $count[0] : 0;

		$result = $this->db->query('SELECT ' . $fields . ' FROM ' . $tb . ' ' . $wheres . $sort . $limit);
		if(!empty($result))
		{
			for($i=0; $i<$result->num_rows; $i++)
			{
				$rows[] = $result->fetch_array(MYSQLI_ASSOC);
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
		if(!empty($this->fields))
		{
			$fields = null;
			foreach($this->fields as $field=>$value)
			{
				$fields[] = $field . '=' . (is_string($value) ? "'" . $value . "'" : $value);
			}
			$fields = join(',', $fields);

			$this->db->query('INSERT INTO ' . $tb . ' SET ' . $fields);

			return true;
		}

		return false;
	}

	/**
	* @Desc Data Update
	* @Param mixed $tb
	* @Return boolean
	*/
	public function update($tb = null)
	{
		if(!empty($this->fields))
		{
			$fields = null;
			foreach($this->fields as $key=>$value)
			{
				$fields[] = $key . '=' . (is_string($value) ? "'" . $value . "'" : $value);
			}
			$fields = join(',', $fields);

			$wheres = null;
			if(!empty($this->where))
			{
				foreach($this->where as $key=>$value)
				{
					$wheres[] = $key . '=' . (is_string($value) ? "'" . $value . "'" : $value);
				}
				$wheres = join(' AND ', $wheres);
			}

			$this->db->query('UPDATE ' . $tb . ' SET ' . $fields . ' ' . $wheres);

			return true;
		}

		return false;
	}

	/**
	* @Desc Data Delete
	* @Param mixed $tb
	* @Return boolean
	*/
	public function remove($tb = null)
	{
		$wheres = null;
		if(!empty($this->where))
		{
			foreach($this->where as $key=>$value)
			{
				$wheres[] = $key . '=' . (is_string($value) ? "'" . $value . "'" : $value);
			}
			$wheres = join(' AND ', $wheres);
		}

		$this->db->query('DELETE FROM ' . $tb . ' ' . $where);

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
