<?php 
/**
 * 获取用户详细信息 
 * 
 */
 
require '../../common.php';
$info['target'] = !empty($_REQUEST['target']) ? trim($_REQUEST['target']) : ''; 
$info['akey'] = !empty($_REQUEST['akey']) ? trim($_REQUEST['akey']) : '';
$info['skey'] = !empty($_REQUEST['skey']) ? trim($_REQUEST['skey']) : ''; 
$info['access_token'] = !empty($_REQUEST['access_token']) ? trim($_REQUEST['access_token']) : ''; 
$info['access_token_secret'] = !empty($_REQUEST['access_token_secret']) ? trim($_REQUEST['access_token_secret']) : ''; 
$info['uid_or_name'] = !empty($_REQUEST['uid_or_name']) ? trim($_REQUEST['uid_or_name']) : ''; 
$datatype = isset($_REQUEST['datatype']) && in_array($_REQUEST['datatype'], array('json','serialize','xml')) ? $_REQUEST['datatype'] : 'json';

$warning = "";
foreach($info as $key=>$val)
{
    $warning .= empty($val) ? "{$key}不能为空 " : "";
}
if(!empty($warning))
{
    display_response($datatype, array('errno'=>-200, 'errmsg'=>$warning));
    exit;
}

if(in_array($info['target'], array("sina","qq","163","sohu")))
{
    $clientname = "cls_client".$info['target'];
    $client = new $clientname($info['akey'] , $info['skey'] , $info['access_token'] , $info['access_token_secret']);
    $result = $client->show_user($info['uid_or_name']);
}
else 
{
    $warning = "不支持该微博平台（{$info['target']}）的接口";
    display_response($datatype, array('errno'=>-200, 'errmsg'=>$warning));
    exit;
}

display_response($datatype, $result);