<?php

session_start();
include_once( '../common.php' );
include_once( 'config.php' );


$results = get_access_token(TARGET, WB_AKEY , WB_SKEY, $_SESSION['keys']['request_token'], $_SESSION['keys']['request_secret']);

$_SESSION['last_key'] = $results;
var_export($results);
//echo "<br>";

?>
授权完成,<a href="weibolist.php">进入你的微博列表页面</a><br>
授权完成,<a href="test.php">进入test页面</a>
