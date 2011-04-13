<?php
/**
 * 缓存控制类
 * @package Helper
 * @author Chengxuan <chengxuan@leju.sina.com.cn>
 * @version #Id:mc.class.php 2010-6-14 上午11:20:40#
 */
class cls_mc
{
	/**
	 * 缓存前缀
	 * @var string
	 */
	protected static $prefix;

	/**
	 * Memcache对象
	 * @var Memcache
	 */
	protected static $_self;

	/**
	 * 构造方法
	 */
	public function  __construct()
	{
		if(!self::$_self instanceof Memcache)
		{
			self::$_self = new Memcache();
			$servers = explode(' ',$_SERVER["SINASRV_MEMCACHED_SERVERS"]);
			foreach($servers as $key => $val)
			{
				$v = explode(':',$val);
				self::$_self->addServer($v[0],$v[1]);
			}
			self::$prefix = $_SERVER["SINASRV_MEMCACHED_KEY_PREFIX"];
		}
		return self::$_self;
	}

	/**
	 * 初始化对象
	 * @return cls_mc
	 */
	static public function init()
	{
		static $object = null;
		$object === null && $object = new self;
		return $object;
	}

	/**
	 * 写缓存
	 * @param	string	$key	键
	 * @param	mixed	$value	值
	 * @param	int		$time	时间
	 * @return boolean
	 */
	public function set($key, $value, $time = '300')
	{
		return self::$_self->set(self::$prefix.$key, $value, 0, $time);
	}

	/**
	 * 读缓存数据
	 * @param	string	$key	键
	 * @return 	mixed
	 */
	public function get($key)
	{
		return self::$_self->get(self::$prefix.$key);
	}

	/**
	 * 删除缓存
	 * @param	string	$key	键
	 * @return 	boolean
	 */
	public function delete($key)
	{
		return self::$_self->delete(self::$prefix.$key);
	}

	/**
	 * 写缓存(支持动态参数)
	 * @param	string	$key	键
	 * @param	array	$param	动态参数
	 * @param	mixed	$value	值
	 * @param	int		$time	时间
	 * @return	boolean
	 */
	public function set_data($key, $param, $value, $time = '300')
	{
		return $this->set(self::get_key($key, (array)$param), $value, $time);
	}

	/**
	 * 读缓取数据(支持动态参数)
	 * @param	string	$key	键
	 * @param	array	$param	动态参数
	 * @return	mixed
	 */
	public function get_data($key, array $param=null)
	{
		return $this->get(self::get_key($key, $param));
	}

	/**
	 * 删除缓存数据(支持动态参数)
	 * @param	string	$key	键
	 * @param	array	$param	动态参数
	 * @param	boolean
	 */
	public function delete_data($key, array $param=null)
	{
		return $this->delete(self::get_key($key, $param));
	}

	/**
	 * 通过基本KEY+参数生成新KEY
	 * @param	string	$key	键
	 * @param	array	$param	动态参数
	 * @param	boolean	$md5	强制使用MD5
	 * @return	string
	 */
	static public function get_key($key, array $param=null, $md5=false)
	{
		$result = $key;
		if($param)
		{
			ksort($param);
			foreach($param as $key=>$value)
			{
				if($value)
				{
					$result .= "_{$key}_{$value}";
				}
			}
		}

		($md5 || strlen($result) > 128) && $result = md5($result);
		return $result;
	}

	/**
	 * 清理所有的项目
	 * @return	boolean
	 */
	public function flush()
	{
		return self::$_self->flush();
	}
}
