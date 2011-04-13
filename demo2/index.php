<?php

session_start();
include_once( 'http.class.php' );
//if( isset($_SESSION['last_key']) ) header("Location: weibolist.php");
include_once( 'config.php' );

$url = "http://t.house.sina.com.cn/mblog/api/oauth/get_request_token.php";
$callback = "http://t.house.sina.com.cn/mblog/demo2/callback.php";
$data = array("target"=>TARGET, "akey"=>WB_AKEY , "skey"=>WB_SKEY, "callback"=>$callback);
$results = json_decode(cls_http::init()->post($url,$data),true);
$_SESSION['keys'] = $results['result'];

var_export($results);
?>
<a href="<?=$results['result']['request_url']?>">Use Oauth to login</a>
