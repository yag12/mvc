<?php
/**
*	@File : Cached.php
*	@Desc : Cached Class
*			$cache = new Cached(array(
*				"servers" => array(
*					array("127.0.0.1", 11211, 50),
*					array("127.0.0.2", 11211, 30),
*					array("127.0.0.3", 11211, 20),
*				),
*				"prefix_key" => "gameonstudio"
*			));
*
*			$cache->set($key, $value);
*			$cache->set(array(
*				$key1 => $value1,
*				$key2 => $value2,
*				$key3 => $value3,
*			));
*
*			$data = $cache->get($key);
*			$datas = $cache->get(array($key1, $key2, $key3));
*
*			$cache->del($key);
*			$cache->del(array($key1, $key2, $key3));
*
*	@Auth : 정갑기
*	@Date : 0000. 00. 00
*/
class Cached
{
	/**
	* @param new Memcached
	*/
	static $memcache;

	/**
	* 디폴트 상수 값
	*/
	const DEFAULT_SERVER = '127.0.0.1';
	const DEFAULT_PORT = 11211;
	const DEFAULT_TIMEOUT = 100;

	/**
	* construct
	* @param array $config array("servers" => array(), "prefix_key" => "")
	*/
	public function __construct($config = array())
	{
		if(!class_exists('Cached'))
		{
            return false;
        }
        else
        {
			$servers = !empty($config['servers']) ? $config['servers'] : array();
			$prefix_key = !empty($config['prefix_key']) ? $config['prefix_key'] : null;
	
			self::$memcache = new Memcached;
			if(!empty($servers))
			{
				self::$memcache->addServers($servers);
			}
			else
			{
				self::$memcache->addServer(self::DEFAULT_SERVER, self::DEFAULT_PORT);
			}
	
			// 해쉬 알고리즘
			self::$memcache->setOption(Memcached::OPT_HASH, Memcached::HASH_MURMUR);
			// 데이터 압축
			self::$memcache->setOption(Memcached::OPT_COMPRESSION, TRUE);
			// 바이너리 프로토콜
			self::$memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
			// 컨넷 타임아웃
			self::$memcache->setOption(Memcached::OPT_CONNECT_TIMEOUT, self::DEFAULT_TIMEOUT);

			if(!empty($prefix_key))
			{
				// 접두사 - 서버 구분시
				self::$memcache->setOption(Memcached::OPT_PREFIX_KEY, $prefix_key);
			}
		}
	}

	/**
	* memcached set
	* - set : Memcache::set($key, $value[, $expiration]);
	* @param string $key 키
	* @param mixed $value 값
	* @param int $expiration 유효시간
	*
	* - multi set : Memcache::set(array($key1=>$value1, $key2=>$value2)[, $expiration])
	* @param array $arrays 배열(키=>값)
	* @param int $expiration 유효시간
	*
	* @return boolean
	*/
	static public function set()
	{
		if(!class_exists('Cached'))
		{
			return false;
		}

		if(!empty(self::$memcache))
		{
			$expiration = null;
			$args_length = func_num_args();
			if ($args_length > 0)
			{
				$args = func_get_args();

				if(is_array($args[0]))
				{
					if(!empty($args[1]) && is_numeric($args[1]))
					{
						$expiration = $args[1];
					}

					self::$memcache->setMulti($args[0], $expiration);
				}
				elseif(is_string($args[0]))
				{
					if(!empty($args[2]) && is_numeric($args[2]))
					{
						$expiration = $args[2];
					}

					if(!empty($args[1]))
					{
						self::$memcache->set($args[0], $args[1], $expiration);
					}
				}
			}

			if(self::$memcache->getResultCode() === Memcached::RES_SUCCESS)
			{
				return true;
			}
		}

		return false;
	}

	/**
	* memcached get
	* - get : Memcache::get($key[, $callback]);
	* @param string $key 키
	* @param callback $callback 콜백함수
	* Read-through caching callback : function callback($memc, $key, &$value){ return true; }
	* 데이터가 없을 경우 콜백함수 호출
	*
	* - multi get : Memcache::get(array($key1, $key2)[, $callback]);
	* @param array $keys 배열(키)
	* @param callback $callback 콜백함수
	* Read-through caching callback : function callback($memc, $key, &$value){ return true; }
	* 데이터가 있는 경우 콜백함수 호출
	*
	* @return mixed
	*/
	static public function get()
	{
		if(!class_exists('Cached'))
		{
			return false;
		}

		if(!empty(self::$memcache))
		{
			$datas = null;
			$callback = null;
			$args_length = func_num_args();
			if ($args_length > 0)
			{
				$args = func_get_args();

				if(!empty($args[1]))
				{
					$callback = $args[1];
				}

				if(is_array($args[0]))
				{
					$records = null;
					self::$memcache->getDelayed($args[0], false);
					foreach($args[0] as $key) $records[$key] = null;
					while(($result = self::$memcache->fetch()) != FALSE)
					{
						$key = $result['key'];
						$value = $result['value'];
						$records[$key] = $value;
					}

					foreach($records as $key=>$value)
					{
						if($value === null)
						{
							$value = self::$memcache->get($key, $callback);
						}

						$datas[] = $value;
					}
				}
				elseif(is_string($args[0]))
				{
					$datas = self::$memcache->get($args[0], $callback);
				}
			}

			if(self::$memcache->getResultCode() === Memcached::RES_SUCCESS)
			{
				return $datas;
			}
		}

		return false;
	}

	/**
	* memcached delete
	*
	* @param array or string $key 키
	* @return boolean
	*/
	static public function del($key)
	{
		if(!class_exists('Cached'))
		{
			return false;
		}

		if(!empty(self::$memcache))
		{
			if(!empty($key))
			{
				if(is_array($key))
				{
					foreach($key as $k) self::del($k);
				}
				elseif(is_string($key))
				{
					self::$memcache->delete($key);
				}
			}

			if(self::$memcache->getResultCode() === Memcached::RES_SUCCESS)
			{
				return true;
			}
		}

		return false;
	}

	/**
	* Decrement numeric item's value 
	* 
	* @param string $key 키
	* @param int $offset 감소값
	* @return int or boolean
	*/
	static public function decrement($key, $offset = 1)
	{
		if(!class_exists('Cached'))
		{
			return false;
		}

		if(!empty(self::$memcache))
		{
			if(empty($offset) || !is_numeric($offset))
			{
				$offset = 1;
			}

			if(!empty($key))
			{
				self::$memcache->decrement($key, $offset);
			}

			if(self::$memcache->getResultCode() === Memcached::RES_SUCCESS)
			{
				return self::get($key, array('Cached', 'callback'));
			}
		}

		return false;
	}

	/**
	* Increment numeric item's value
	* 
	* @param string $key 키
	* @param int $offset 증가값
	* @return int or boolean
	*/
	static public function increment($key, $offset = 1)
	{
		if(!class_exists('Cached'))
		{
			return false;
		}

		if(!empty(self::$memcache))
		{
			if(empty($offset) || !is_numeric($offset))
			{
				$offset = 1;
			}

			if(!empty($key))
			{
				self::$memcache->increment($key, $offset);
			}

			if(self::$memcache->getResultCode() === Memcached::RES_SUCCESS)
			{
				return self::get($key, array('Cached', 'callback'));
			}
		}

		return false;
	}

	/**
	* default callback
	*
	* @param Memcached $memc
	* @param mixed $key
	* @param mixed $value
	* @return boolean
	*/
	static public function callback($memc, $key, &$value)
	{ 
		$value = 0; 

		return true; 
	}
}
