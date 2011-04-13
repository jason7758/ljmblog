<?php 
session_start();
include_once( 'http.class.php' );
include_once( 'config.php' );

$data = array(
'akey'=> WB_AKEY,
'skey'=> WB_SKEY,
'access_token'=>$_SESSION['last_key']['access_token'],
'access_token_secret'=>$_SESSION['last_key']['access_secret'],
'target'=>TARGET
);

$url = "http://t.house.sina.com.cn/mblog/api/mblog/public_timeline.php";
$results = cls_http::init()->post($url,$data);
$results = json_decode($results,true);
var_export($results);