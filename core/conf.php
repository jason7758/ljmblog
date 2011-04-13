<?php
define("DEBUG", false);

define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

define("ROOT_DIR",dirname(dirname(__FILE__)));
define("TPL_DIR", ROOT_DIR.'/templates');
define("TIME", time());
define("DATE", date("Y-m-d", TIME));

$__db__ = array(
	0 => array(
		'user' => $_SERVER['SINASRV_DB2_USER'],
		'password' => $_SERVER['SINASRV_DB2_PASS'],
		'host' => $_SERVER['SINASRV_DB2_HOST'],
		'port' => $_SERVER['SINASRV_DB2_PORT'],
		'database' => $_SERVER['SINASRV_DB2_NAME']
	),
	1 => array(
		'user' => $_SERVER['SINASRV_DB2_USER_R'],
		'password' => $_SERVER['SINASRV_DB2_PASS_R'],
		'host' => $_SERVER['SINASRV_DB2_HOST_R'],
		'port' => $_SERVER['SINASRV_DB2_PORT_R'],
		'database' => $_SERVER['SINASRV_DB2_NAME_R']
	)
);
