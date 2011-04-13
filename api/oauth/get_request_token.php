<?php

include_once( '../../common.php' );

$target = $_POST['target'];
$app_key = $_POST['akey'];
$app_secret = $_POST['skey'];
$callback = $_POST['callback'];

$result = get_request_token($target, $app_key , $app_secret, $callback);
echo json_encode(array('errno'=>1,'errmsg'=>'成功','result'=>$result));


