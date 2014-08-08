<?php
abstract class Controller
{	
	/**
	* @Desc 컨트롤러명
	* @Var array
	*/
	protected $contollerName = 'Index';

	/**
	* @Desc 액션명
	* @Var array
	*/
	protected $actionName = 'index';

	/**
	* @Desc 데이터 형식[default:html] (Type:html, json, xml, xls)
	* @Var mixed
	*/
	protected $dataType = 'html';

	/**
	* @Desc 뷰 파일 위치
	* @Var mixed
	*/
	protected $scripts = 'scripts';

	/**
	* @Desc 파라미터
	* @Var array
	*/
	protected $params = array();

	/**
	* @Desc 레이아웃
	* @Var mixed
	*/
	protected $layout = 'index.php';

	/**
	* @Desc DB
	* @Var Database
	*/
	protected $db = null;

	/**
	* @Desc Func
	* @Var Object
	*/
	protected $func = null;

	/**
	* @Desc Session Data
	* @Var mixed
	*/
	protected $sess = null;

	/**
	* @Desc Config Data
	* @Var array
	*/
	protected $config = array();

	/**
	* @Desc Memcached
	* @Var array
	*/
	protected $cache = null;

	/**
	* @Desc Auth
	* @Var mixed
	*/
	protected $auth = false;

	/**
	* @Desc start up
	* @Param mixed $controllerName
	* @Param mixed $actionName
	* @Param mixed $dataType
	* @Param array $params
	* @Return mixed or boolean
	*/
	final public function startup($controllerName = null, $actionName = null, $dataType = 'html', $params = array())
	{
		$this->getConfig();
		
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
		$this->dataType = $dataType;

		// 모바일 웹인 경우
		$detect = new MobileDetect;
		if($detect->isMobile())
		{
			$this->scripts = 'mobiles';
		}

		// 인증 체크
		if($this->auth === true || $this->auth[$actionName] === true)
		{
			if(!empty($this->config['auth']) && empty($_SESSION['id']))
			{
				$request_uri = urlencode($_SERVER['REQUEST_URI']);
				$auth = array_merge($this->config['auth'], array('uri' => $request_uri));
				header('Location:' . $this->url($auth));
				return false;
			}
		}

		// 액션함수 호출
		if(is_callable(array($this, $actionName), false) === true)
		{
			$this->params = !empty($params) ? $params : null;
			$this->setParam('_GET', $_GET);
			$this->setParam('_POST', $_POST);

			if($dataType === 'xml')
			{
				// Xml 업로드
				$assoc = XmlRead::toArray(XmlRead::uploadXml());
				if(!empty($assoc))
				{
					$this->setParam('_XML', $assoc);
				}
			}

			// Memcached 설정
			if(!empty($this->config['memcached']))
			{
				$this->cache = new Cached($this->config['memcached']);
			}

			// Database 설정
			if(!empty($this->config['database']))
			{
				$this->db = new Database($this->config['database'], $this->cache);
			}

			// Session
			if(!empty($_SESSION))
			{
				$this->sess = $_SESSION;
			}

			$this->init();

			$result = call_user_func(array($this, $actionName));
		
			// 결과 값이 존재하고 데이터형식이 html인 경우 레이아웃 출력
			if(empty($result) && $this->dataType === 'html')
			{
				$this->layout();
				return true;
			}
			else
			{
				switch($this->dataType)
				{
					case 'json':
						$result = json_encode($result);
						print_r($result);
						return true;
						break;
					case 'xml':
						$XmlConstruct = new XmlConstruct('root'); 
						$XmlConstruct->fromArray($result); 
						$XmlConstruct->output();
						return true;
						break;
					case 'xls':
						$excel = new Excel;
						$excel->setFileName($actionName);
						//$excel->setSheetTitle($actionName);
						$excel->setData($result);
						$excel->output();
						return true;
						break;
					default:
						return $result;
						break;
				}
			}
		}
		else
		{
			// Error
			throw new Exception("Action Was Not Found : " . $controllerName . "::" . $actionName, 404);
		}

		return false;
	}

	/**
	* @Desc 시스템 설정
	* @Param void
	* @Return void
	*/
	private function getConfig()
	{
		$configFile = ROOT . '/config/config.php';

		if(is_file($configFile))
		{
			require_once $configFile;

			$this->config = !empty($config) ? $config : null;
		}
	}

	/**
	* @Desc 디자인 레이아웃
	* @Param void
	* @Return void
	*/
	private function layout()
	{
		$layoutFile = ROOT . '/' . $this->scripts . '/layout/' . $this->layout;
		if(is_file($layoutFile))
		{
			echo $this->getContents($layoutFile, $this->params);
		}
	}

	/**
	* @Desc 컨텐츠 가져오기
	* @Param mixed $file
	* @Param array $data
	* @Return mixed
	*/
	private function getContents($file = null, $data = array())
	{
		$contents = null;
		if($this->dataType == 'html')
		{
			if(!empty($data))
			{
				extract($data);
			}

			if(is_file($file))
			{	
				ob_start();
				require $file;
				$contents = ob_get_clean();
			}
			else
			{
				// Error Message
				throw new Exception("File Not Found : " . $file, 404);
			}
		}

		return $contents;
	}

	/**
	* @Desc 디자인 컨텐츠 뷰
	* @Param mixed $name
	* @Param array $data
	* @Param mixed $scope
	* @Return void
	*/
	protected function partials($name = null, $data = array(), $scope = null)
	{
		if($this->dataType == 'html')
		{
			$view = ROOT . '/' . $this->scripts . '/partials/' . $name;
			if($scope === null)
			{
				return $this->getContents($view, $data);
			}

			$this->$scope = $this->getContents($view, $data);
		}
	}

	/**
	* @Desc 켄턴츠 뷰
	* @Param mixed $name
	* @Param mixed $directory
	* @Param mixed $scope
	* @Return void
	*/
	protected function render($name = null, $directory = null, $scope = 'contents')
	{
		if($this->dataType == 'html')
		{
			if(empty($directory))
			{
				$directory = ucfirst($this->controllerName);
			}

			$view = ROOT . '/' . $this->scripts . '/view/' . $directory . '/' . $name;
			$this->$scope = $this->getContents($view, $this->params);
		}
	}

	/**
	* @Desc 레이아웃
	* @Param mixed $name
	* @Return void
	*/
	protected function setLayout($name = null)
	{
		$this->layout = !empty($name) ? $name : $this->layout;
	}

	/**
	* @Desc 파라미터 저장
	* @Param mixed $key
	* @Param mixed $value
	* @Return void
	*/
	protected function setParam($key = null, $value = null)
	{
		if(!empty($key) && !empty($value))
		{
			$this->params[$key] = $value;
		}
	}

	/**
	* @Desc init
	* @Param void
	* @Return void
	*/
	public function init(){ }

	/**
	* @Desc js url
	* @Param mixed $urls
	* @Param boolean $bool
	* @Param mixed $default
	* @Return mixed
	*/
	public function js($name = null, $bool = false, $default = 'js')
	{
		$url = substr(ROOT, strlen($_SERVER['DOCUMENT_ROOT']));
		$url = $url . '/' . $default . '/' . $name;

		if($bool === true)
		{
			$url = '<script type="text/javascript" src="' . $url . '"></script>';
		}

		return $url;
	}

	/**
	* @Desc css url
	* @Param mixed $urls
	* @Param boolean $bool
	* @Param mixed $default
	* @Return mixed
	*/
	public function css($name = null, $bool = false, $default = 'css')
	{
		$url = substr(ROOT, strlen($_SERVER['DOCUMENT_ROOT']));
		$url = $url . '/' . $default . '/' . $name;

		if($bool === true)
		{
			$url = '<link rel="stylesheet" href="' . $url . '" />';
		}

		return $url;
	}

	/**
	* @Desc image url
	* @Param mixed $urls
	* @Param boolean $bool
	* @Param mixed $default
	* @Return mixed
	*/
	public function image($name = null, $bool = false, $default = 'image')
	{
		$url = substr(ROOT, strlen($_SERVER['DOCUMENT_ROOT']));
		$url = $url . '/' . $default . '/' . $name;

		if($bool === true)
		{
			$url = '<img src="' . $url . '" border="0" />';
		}

		return $url;
	}

	/**
	* @Desc url
	* @Param array or false $urls
	* @Return mixed
	*/
	public function url($urls = array())
	{
		$url = substr(ROOT, strlen($_SERVER['DOCUMENT_ROOT']));
		$params = null;
		$controller = $this->controllerName;
		$action = $this->actionName;

		if($urls !== false)
		{
			if(is_array($urls))
			{
				foreach($urls as $key=>$value)
				{
					if($key === 'controller')
					{
						$controller = $value;
					}
					elseif($key === 'action')
					{
						$action = $value;
					}
					elseif(is_string($key))
					{
						$params = (!empty($params) ? $params . '&' : '') . $key . '=' . $value;
					}
					else
					{
						$params = (!empty($params) ? $params . '&' : '') . $value;
					}
				}
			}

			if(strtolower($controller) != 'index' || $action != 'index')
			{
				$url = $url . '/' . $controller . '/' . $action;
			}

			if(!empty($params))
			{
				$url = $url . '?' . $params;
			}
		}

		return $url;
	}
}
