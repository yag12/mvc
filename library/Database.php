<?php
class Database
{
	/**
	* @Desc Config
	* @Var array
	*/
	public $config = array();

	/**
	* @Desc Memcached
	* @Var array
	*/
	public $cache = null;

	/**
	* @Desc Construct
	* @Param array $config
	* @Param Cached $cache
	* @Return void
	*/
	public function __construct($config = array(), &$cache = null)
	{
		$this->config = $config;
		$this->cache = &$cache;
		$this->connection();
	}

	/**
	* @Desc Get Model
	* @Param mixed $name
	* @Return void
	*/
	public function getModel($name = null)
	{
		$modelObj = null;
		$name = ucfirst($name);
		$modelName = $name . 'Model';
		$modelFile = ROOT . '/model/' . $name . '.php';

		if(is_file($modelFile))
		{
			require_once $modelFile;

			if(class_exists($modelName))
			{
				$this->$modelName = new $modelName($this);
			}
		}

		return $this->$modelName;
	}

	/**
	* @Desc Connection
	* @Param void
	* @Return void
	*/
	protected function connection()
	{
		if(!empty($this->config))
		{
			$number = 0;
			foreach($this->config as $name=>$config)
			{
				switch($config['type'])
				{
					case 'mongo':
						$this->$name = $this->connectMongo($config);
						break;
					case 'mysql':
						$this->$name = $this->connectMysql($config);
						break;
					case 'mysqli':
						$this->$name = $this->connectMysqli($config);
						break;
				}

				if($number == 0)
				{
					$this->default = $this->$name;
				}

				$number++;
			}
		}
	}

	/**
	* @Desc MongoDb Connection
	* @Param array $config
	* @Return Mongo
	*/
	private function connectMongo($config = array())
	{
		$server = 'mongodb://' . $config['host'] . (!empty($config['port']) ? ':' . $config['port'] : '');
		$options = array('connect' => true);
		$mongo = null;
		$db = null;

		if(class_exists("MongoClient"))
		{
			$mongo = new MongoClient($server, $options);
		}
		elseif(class_exists("Mongo"))
		{
			$mongo = new Mongo($server, $options);
		}

		if(!empty($mongo))
		{
			$db = $mongo->selectDB($config['db']);
		}

		return $db;
	}

	/**
	* @Desc Mysql Connection
	* @Param array $config
	* @Return Mysql
	*/
	private function connectMysql($config = array())
	{
		$server = $config['host'] . (!empty($config['port']) ? ':' . $config['port'] : '');
		$connect = mysql_connect($server, $config['user'], $config['passwd']) or die(mysql_error());
		$db = mysql_select_db($config['db'], $connect);

		return $db;
	}

	/**
	* @Desc Mysqli Connection
	* @Param array $config
	* @Return Mysqli
	*/
	private function connectMysqli($config = array())
	{
		$port = !empty($config['port']) ? $config['port'] : null;
		$db = new mysqli($config['host'], $config['user'], $config['passwd'], $config['db'], $port);
		if (mysqli_connect_error()) {
			die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
		}

		return $db;
	}
}
