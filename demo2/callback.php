<?php

session_start();
include_once( 'http.class.php' );
include_once( 'config.php' );

$url = "http://t.house.sina.com.cn/mblog/api/oauth/get_access_token.php?".$_SERVER['QUERY_STRING'];

$data = array("target"=>TARGET, "akey"=>WB_AKEY , "skey"=>WB_SKEY, "request_token"=>$_SESSION['keys']['request_token'], "request_secret"=>$_SESSION['keys']['request_secret']);
$results = json_decode(cls_http::init()->post($url,$data),true);
$_SESSION['last_key'] = $results['result'];
var_export($results);
//echo "<br>";

?>
授权完成,<a href="weibolist.php">进入你的微博列表页面</a><br>
授权完成,<a href="test.php">进入test页面</a>
