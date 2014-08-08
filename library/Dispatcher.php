<?php
class Dispatcher
{
	/**
	* @Desc Controller Default Name
	* @Var mixed
	*/
	private $controller = 'index';

	/**
	* @Desc Action Default Name
	* @Var mixed
	*/
	private $action = 'index';

	/**
	* @Desc Data Type
	* @Var mixed
	*/
	private $dataType = 'html';

	/**
	* @Desc Construct
	* @Param mixed $requestUri
	* @Return void
	*/
	public function __construct($requestUri = null)
	{
		$this->setRequire();
		$this->dispatch($requestUri);
	}

	/**
	* @Desc startup
	* @Param void
	* @Return void
	*/
	static public function startup()
	{
		return new Dispatcher;
	}

	/**
	* @Desc Set Require
	* @Param void
	* @Return void
	*/
	static public function setRequire()
	{
		$library = dirname(__FILE__);

		require_once $library . '/Function.php';
		require_once $library . '/Database.php';
		require_once $library . '/Model.php';
		require_once $library . '/Model/Mongo.php';
		require_once $library . '/Model/Mysql.php';
		require_once $library . '/Model/Mysqli.php';
		require_once $library . '/Xml/Read.php';
		require_once $library . '/Xml/Construct.php';
		require_once $library . '/Cached.php';
		require_once $library . '/Excel.php';
		require_once $library . '/Controller.php';
		require_once $library . '/MobileDetect.php';
		require_once $library . '/FileUpload.php';
		require_once $library . '/ThumbImage.php';
	}

	/**
	* @Desc Dispatch
	* @Param mixed $requestUri
	* @Return void
	*/
	private function dispatch($requestUri = null)
	{
		if(empty($requestUri))
		{
			if(isset($_SERVER['REQUEST_URI']))
			{
				$requestUri = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
				$requestUri = str_replace("//", "/",  $requestUri);
				$httpHost = dirname(dirname(__FILE__));
				if(strpos($requestUri, $httpHost) === 0)
				{
					$requestUri = substr($requestUri, strlen($httpHost));
				}
			}
		}

		$uri = $requestUri;
		if(strpos($uri, "?") !== false)
		{
			list($uri, $params) = preg_split("/\?/", $uri);
		}

		if($uri != '/')
		{
			list($temp, $controllerName, $actionName) = preg_split("/\//", $uri);
		}

		if(!empty($actionName) && strpos($actionName, ".") !== false)
		{
			list($actionName, $dataType) = preg_split("/\./", $actionName);
		}

		if(!empty($controllerName)) $this->controller = $controllerName;
		if(!empty($actionName)) $this->action = $actionName;
		if(!empty($dataType)) $this->dataType = $dataType;

		$this->initContoller($this->controller, $this->action, $this->dataType);
		//dispatcher::initContoller(Controller Name, Action Name, Data Type, Params);
	}

	/**
	* @Desc Controller
	* @Param mixed $controller
	* @Param mixed $action
	* @Param mixed $type
	* @Param array $params
	* @Return mixed or false 
	*/
	static public function initContoller($controller = null, $action = null, $type = 'html', $params = array())
	{
		$controller = empty($controller) ? $this->controller : $controller;
		$action = empty($action) ? $this->action : $action;

		$controller = ucfirst($controller);
		$controllerFile = ROOT . '/controller/' . $controller . '.php';
		try{
			if(is_file($controllerFile))
			{
				require_once $controllerFile;

				$controllerName = $controller . 'Controller';
				if(class_exists($controllerName))
				{
					$controllerObj = new $controllerName;
					return $controllerObj->startup($controller, $action, $type, $params);
				}
			}
			else
			{
				// Error
				throw new Exception("File Not Found : " . $controllerFile, 404);
			}
		}
		catch(Exception $e)
		{
			if($type == 'html')
			{
				print_r($e);
			}

			return $e;
		}

		return false;
	}
}
