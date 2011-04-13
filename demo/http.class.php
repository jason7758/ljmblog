<?php

/**
 * HTTP操作类
 * @package Helper
 * @author Chengxuan <chengxuan@leju.sina.com.cn>
 * @version #Id:http.php 2010-5-21 上午11:18:42#
 */
class cls_http
{
	/**
	 * CURL资源句柄
	 * @var resource
	 */
	protected $curl = null;
	
	
	/**
	 * 构造方法（锁定只有一个对象）
	 */
	protected function __construct() {}
	
	/**
	 * 析构方法
	 */
	public function __destruct()
	{
		is_resource($this->curl) && curl_close($this->curl);
	}
	
	/**
	 * 初始化对象
	 * @return Cls_Http
	 */
	static public function init()
	{
		static $object = null;
		$object === null && $object = new self;
		return $object;		
	}
	
	/**
	 * 通过GET取得一条数据
	 * @param string	$url		指定URL
	 * @param string	$cookie		COOKIE字符中
	 * @param string	$referer	指定来源
	 * @param string	$userAgent	指定用户标识（浏览器）
	 */
	public function get($url, $cookie=null, $referer=null, $userAgent=null)
	{
		$this->curl_init();
		$this->setOption(array(
			CURLOPT_URL			=> $url,
			CURLOPT_COOKIE		=> $cookie,
			CURLOPT_REFERER		=> $referer,
			CURLOPT_USERAGENT	=> $userAgent,
		));

		return curl_exec($this->curl);
	}
	
	/**
	 * 通过POST取得一条数据
	 * @param string	$url			指定URL
	 * @param mixed		$data			提交的数据（数组或查询字符串，如果是传文件，必需用数组，文件名值前面加@）
	 * @param string	$cookie			COOKIE字符中
	 * @param string	$referer		指定来源
	 * @param string	$userAgent		指定用户标识（浏览器）
	 */
	public function post($url, $data, $cookie=null, $referer=null, $userAgent=null)
	{
		$this->curl_init();
		$this->setOption(array(
			CURLOPT_URL				=> $url,
			CURLOPT_COOKIE			=> $cookie,
			CURLOPT_REFERER			=> $referer,
			CURLOPT_USERAGENT		=> $userAgent,
			CURLOPT_POST			=> 1,
			CURLOPT_POSTFIELDS		=> $data,
		));
		
		return curl_exec($this->curl);
	}
	
	/**
	 * 取得最后一次产生的错误
	 * @return mixed 有错误返回Cls_error，无错误返回false
	 */
	public function error()
	{
		$message = curl_error($this->curl);
		$code = curl_errno($this->curl);
		return $message ? new Cls_Error($message, $code) : false;
	}
	
	/**
	 * 重新初始化CURL
	 * @return resource
	 */
	protected function curl_init()
	{
		is_resource($this->curl) && curl_close($this->curl);
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		return $this->curl;
	}
	
	/**
	 * 设置对象属性
	 * @param array $options
	 * @return Cls_Http
	 */
	protected function setOption(array $options)
	{
		curl_setopt_array($this->curl, $options);
		return $this;
	}
}