<?php

session_start();
include_once( '../common.php' );
//if( isset($_SESSION['last_key']) ) header("Location: weibolist.php");
include_once( 'config.php' );

$callback = "http://t.house.sina.com.cn/mblog/demo/callback.php";
$results = get_request_token(TARGET, WB_AKEY , WB_SKEY, $callback);

$_SESSION['keys'] = $results;

var_export($results);
?>
<a href="<?=$results['request_url']?>">Use Oauth to login</a>
