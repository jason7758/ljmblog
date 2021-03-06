<?php
class cls_pdo
{
	/**
	 * 是否开启DEBUG
	 * @var $debug boolean
	 */
//	private $debug = false;

	/**
	 * 是否开启缓存
	 * @var boolean
	 */
	public $cache = false;
	
	/**
	 * PDO操作对象
	 * @var PDO
	 */
	protected $db;

	/**
	 * PDO 查询名柄对象
	 * @var PDOStatement
	 */
	private $smf;

	/**
	 * 缓存操作对象
	 * @var cls_mc
	 */
	protected $mc;

	/**
	 * 数据库连接集合
	 * @var array
	 */
	private static $db_link;

	/**
	 * 插入数据返回值
	 */
	protected $primary =  '';

	public function __construct()
	{
		$this->mc = cls_mc::init();
	}

	/**
	 * 取得数据库连接
	 * @param int		$i
	 * @param boolean	$repeat
	 * @param boolean	$force_connect
	 * @return PDO
	 */
	private function get_db($i=1, $repeat=false, $force_connect=false)
	{

		if(!isset(self::$db_link[$i]) || $force_connect)
		{
			global $__db__;
			extract($__db__[$i]);
			
			$dsn = "mysql:host={$host};port={$port};dbname={$database}";
			try {
				self::$db_link[$i] = new PDO($dsn, $user, $password);
//				self::$db_link[$i]->exec("SET character_set_connection=gbk, character_set_results=gbk, character_set_client=binary;");
//				$this->setDb();
			} catch (PDOException $e) {
				if(!$repeat)	return $this->get_db($i, true);	//重试
				$this->throw_error($e->getMessage());
				require ROOT_DIR . '/notice.php';
				exit;
//				echo 'Connection failed: ' . $e->getMessage();
			}
		}
		$this->db = self::$db_link[$i];
		return $this->db;
	}

	/**
	 * 设置数据库字符集
	 * @param unknown_type $charset
	 */
	public function set_db($charset = 'GBK')
	{
		$this->db->exec("SET CHARACTER SET GBK");
	}

	/**
	 * 运行sql
	 *
	 * @param sql $sql
	 */
	public function query($sql)
	{
		$db_router = ('select' == strtolower(substr(trim($sql),0,6))) ? 1 : 0;
		$this->get_db($db_router);
		$this->smf = $this->db->query($sql);
		if(!$this->smf)
		{
			$errorInfo = $this->db->errorInfo();
			if($errorInfo && $errorInfo[1] == 2006)
			{
				$this->get_db($db_router, true, true);
				$this->smf = $this->db->query($sql);
			}

			if(!$this->smf)
			{
				$this->error($this->db, $sql);
				return false;
			}
		}
		return true;

//		try {
//		//
//			$db_router = ('select' == strtolower(substr(trim($sql),0,6))) ? 1 : 0;
//			$this->get_db($db_router);
//			$this->smf = $this->db->prepare($sql);
//
//			if(!$this->smf)
//			{
//				if($this->debug)
//				{
//					echo "sql:".$sql."<hr />";
//					print_r($this->db->errorCode());
//					print_r($this->db->errorInfo());
//					exit;
//				}
//				return false;
//			}
//			$rs = $this->smf->execute();
//			if(!$rs)
//			{
//				$error = "sql:".$sql."<hr />\r\n";
//				$error .= print_r($this->smf->errorInfo(), true). "<hr />\r\n";
//				$error .= cls_debug::trace('sql', false, true). "\r\n";
//
//
//				if($this->debug)
//				{
//					echo $error;
//					exit;
//				}
//				else
//				{
//					error_log($error, E_USER_WARNING);
//				}
//				return false;
//			}
//		//
//		} catch (PDOException $e) {
//			echo 'query failed: ' . $e->getMessage();
//		}
//		return true;
	}
	/**
	 * 取得影响记录数
	 */
	public function row_count()
	{
		if($this->smf INSTANCEOF PDOStatement)
		{
			return $this->smf->rowCount();
		}
		return false;
	}

	/**
	 * 数据库插入操作
	 * @param array		$info
	 * @param string	$table
	 * @param boolean	$check_fields
	 * @param boolean	$ignore
	 * @return int
	 */
	public function insert($info, $table, $check_fields=false, $ignore=false)
	{
		$check_fields && $info = $this->check_fields($table, $info);
		$key = '`'.implode('`,`',array_keys($info)).'`';
		$value = "'".implode('\',\'',array_values($info))."'";

		$ignore = $ignore ? 'IGNORE' : '';
		$sql = "INSERT {$ignore} INTO $table ($key) VALUES ($value)";
		$query = $this->query($sql);
		return $query ? $this->db->lastInsertId($this->primary) : $query;
	}

	/**
	 * 数据库替换操作
	 * @param array		$info
	 * @param string	$table
	 * @param boolean	$check_fields
	 * @return int
	 */
	public function replace_into($info, $table, $check_fields=false)
	{
		$check_fields && $info = $this->check_fields($table, $info);
		$key = '`'.implode('`,`',array_keys($info)).'`';
		$value = "'".implode('\',\'',array_values($info))."'";
		$sql = "replace into $table ($key) values ($value)";
		$rs = $this->query($sql);
		if($rs && $this->primary)
		{
			$rs = $this->db->lastInsertId($this->primary);
		}
		return $rs;
	}

	/**
	 * 数据库更新操作
	 * @param array		$info
	 * @param string	$table
	 * @param boolean	$check_fields
	 * @return mixed
	 */
	public function update($info, $table, $where, $check_fields=false)
	{
		$check_fields && $info = $this->check_fields($table, $info);
//		cls_debug::dump($info, $table);
		$set_info = array();
		foreach ($info as $key => $value)
		{
			$set_info[] = '`'.$key.'` = '."'$value'";
		}
		$set_str = implode(',',$set_info);
		$sql = "update $table set $set_str $where";
		return $this->query($sql);
	}

	/**
	 * 检查字段的合法性
	 * @param string	$table	表名
	 * @param array		$info	数据
	 * @return array			新数据
	 */
	public function check_fields($table, $info)
	{
		$mc_key = $this->mc->get_key('field', array('table'=>$table));

		//取得字段信息
		$fields = $this->mc->get($mc_key);
		if($fields === false)
		{
			$fields = $this->fetch_col("SHOW COLUMNS FROM `{$table}`");
			$this->mc->set($mc_key, $fields);
		}
		
		$result = array();
		foreach($fields as $value)
		{
			isset($info[$value]) && $result[$value] = $info[$value];
		}
		return $result;
	}

	/**
	 * 取符合条件的一条记录
	 *
	 * @param sql $sql
	 * @return array
	 */
	public function fetch($sql, $nocache = false)
	{
		if($this->cache)
		{
			$key = md5($sql);
			$result = $nocache ? '' : $this->mc->get($key);
			if(empty($result))
			{
				$this->query($sql);
				$result = $this->smf->fetch(PDO::FETCH_ASSOC);
				$this->mc->set($key, $result);
			}
		}
		else
		{
			$query = $this->query($sql);
			if(!$query)		return false;
			$result = $this->smf->fetch(PDO::FETCH_ASSOC);
		}
		return $result;
	}
	
	/**
	 * 取符合条件的一列记录
	 *
	 * @param sql $sql
	 * @return array
	 */
	public function fetch_col($sql, $n = 0)
	{
		$query = $this->query($sql);
		if(!$query)		return false;
		return $this->smf->fetchAll(PDO::FETCH_COLUMN, $n);
	}

	/**
	 * 事物封装处理
	 * 没有经过测试
	 * @param array $sql_arr
	 */
	public function run($sql_arr)
	{
		$this->get_db(0);
		$this->db->beginTransaction();
		foreach ($sql_arr as $sql)
		{
			echo $sql.'<br />';
			$rs = $this->db->exec($sql);
			echo $rs.':'.$sql.'<br />';
		}
		if(!$this->db->commit())
		{
			$this->db->rollBack();
			return false;
		}
		return true;
	}
	/**
	 * 取出所有满足条件的数据
	 *
	 * @param string $sql
	 * @return array
	 */
	public function fetch_all($sql, $nocache = false)
	{
		if($this->cache)
		{
			$key = md5($sql);
			$result = $nocache ? '' : $this->mc->get($key);
			if(empty($result))
			{
				$this->query($sql);
				$result = $this->smf->fetchAll(PDO::FETCH_ASSOC);
				$this->mc->set($key, $result);
			}
			else
			{
				//echo 'cached: '.$sql.' <br />';
			}
		}
		else
		{
			$query = $this->query($sql);
			if(!$query)		return false;
			$result = $this->smf->fetchAll(PDO::FETCH_ASSOC);
		}
		return $result;
	}
	public function clear_cache($sql)
	{
		$key = md5($sql);
		$this->mc->set($key, NULL);
	}
	/**
	 * 扩展取单一字段
	 *
	 * @param string $sql
	 * @return str
	 */
	public function fetch_one($sql)
	{
		$query = $this->query($sql);
		if(!$query)		return false;
		$result = $this->smf->fetch();
		return $result[0];
	}

	/**
	 * 处理错误
	 * @param PDO		$pdo
	 * @param string	$sql
	 */
	protected function error($pdo, $sql='')
	{
		list($code, $sql_code, $info) = $pdo->errorInfo();
		$string = "({$code}) $info ($sql_code) [{$sql}]";

		$trace = cls_debug::trace('db', false, true, false);
		$trace_html = nl2br(str_replace(' ', '&nbsp;', $trace));

		$error = "{$string}\r\n{$trace}";
		$error_html = "<hr />{$string}<hr />\r\n{$trace_html}<hr />";

		$this->throw_error($error_html, $error);
	}

	/**
	 * 抛出错误
	 * @param string $error_html
	 * @param string $error
	 */
	protected function throw_error($error_html, $error=null)
	{
		$error === null && $error = $error_html;
		
		//将错误写入日志
		error_log($error);

		//将错误显示在页面上
		trigger_error($error_html, E_USER_WARNING);
	}

	/**
	 * 组装where查询语句
	 * @param unknown_type $arr_info
	 * @param unknown_type $arr_field
	 */
	protected function mark_where_str( $arr_where, $arr_field, $str_replace='{v}' )
	{
		$tmp = array();
		foreach( $arr_field as $k => $v )
		{
			if( isset($arr_where[$k]) ) $tmp[] = str_replace( $str_replace, $arr_where[$k], $v );
		}
		$where = implode( ' and ', $tmp );
		if( $where ) $where = ' where '.$where;
		return (string)$where;
	}

}
