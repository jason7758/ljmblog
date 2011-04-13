<?php
date_default_timezone_set('PRC');
$run_time_start = microtime(true);
require 'core/conf.php';
require 'core/function.php';
$start_time = microtime_float();

if(DEBUG)
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

if(!MAGIC_QUOTES_GPC)
{
    !empty($_POST) && $_POST=daddslashes($_POST);
    !empty($_GET) && $_GET=daddslashes($_GET);
    !empty($_REQUEST) && $_REQUEST=daddslashes($_REQUEST);
    !empty($_COOKIE) && $_COOKIE=daddslashes($_COOKIE);
}
if(!defined("PAGE_CACHE"))
{
	define("PAGE_CACHE", true);
}
if(!PAGE_CACHE)
{
	header("Cache-Control:no-cache,must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
}
if(!defined("SMARTY"))
{
	define("SMARTY", false);
}

function __autoload($class_name)
{
    if(strpos($class_name, '_') === false)
    {
        die('没有此操作');
    }
    else
    {
        $tmp = explode("_", $class_name);

        if($tmp[0] == 'cls')
        {
        	$file = (count($tmp) == 3) 
        		? ROOT_DIR.'/include/'.$tmp[1].'/'.$tmp[2].'.php'
                : ROOT_DIR.'/include/'.$tmp[1].'.class.php'; //无需操作数据库的类

        	if(!file_exists($file))
        	{
        		$file = ROOT_DIR.'/core/class/'.$tmp[1].'.class.php'; //需要操作数据库的类 
        	}      	
            
	        if(file_exists($file)) {
		        require_once($file);
            } else {
                throw new Exception($file."文件无法找到", 1);  
            } 
        }
    }     
}

$php_self = explode('/', $_SERVER['SCRIPT_NAME']);
$php_self = end($php_self);
$php_self = substr($php_self, 0, -4);
$uniqid = uniqid();
if(SMARTY)
{
	$cls_smarty = new cls_smarty();
	$cls_smarty->assign("file_name", basename($_SERVER['SCRIPT_FILENAME']));
	$cls_smarty->assign("php_self", $php_self);
	$cls_smarty->assign('uniqid', $uniqid);
}

//$cookie_user_info = get_user_info();
$user_ip = ip2long($_SERVER['REMOTE_ADDR']);
