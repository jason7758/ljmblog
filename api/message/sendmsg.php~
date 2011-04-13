<?php 
/**
 * 发送消息（支持批量，一次最多100个）
 * @param array $info
 * @param int $info['uid'] 发送人用户ID
 * @param string $info['fuids'] 接收人用户ID
 * @param string $info['content'] 消息内容
 * @param string $info['appname'] 消息来自哪个应用
 */ 
 
require '../../common.php';
$info['uid'] = !empty($_POST['uid']) ? intval($_POST['uid']) : 0;
$info['fuids'] = !empty($_POST['fuids']) ? trim($_POST['fuids']) : '';
$info['appname'] = !empty($_POST['appname']) ? trim($_POST['appname']) : '';
$info['content'] = !empty($_POST['content']) ? trim($_POST['content']) : '';
$datatype = isset($_POST['datatype']) && in_array($_POST['datatype'], array('json','serialize','xml')) ? $_POST['datatype'] : 'json';

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

$cls_message = new cls_message();
$result = $cls_message->send($info);

if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功');
}
display_response($datatype, $response);      