<?php

include_once( '../../common.php' );

$target = $_POST['target'];
$app_key = $_POST['akey'];
$app_secret = $_POST['skey'];
$request_token = $_POST['request_token'];
$request_secret = $_POST['request_secret'];

$result = get_access_token($target, $app_key , $app_secret, $request_token, $request_secret);

echo json_encode(array('errno'=>1,'errmsg'=>'成功','result'=>$result));
