<?php 

/**
 * 获取某人收到的消息
 * @param int $uid 接收人用户ID
 * @param int $page 页码 
 * @param int $pagesize 每页显示数量 
 * @param string $ord 按发布时间排序方式（升序:asc，降序:desc）
 * @param string $appname 消息来自哪个应用
 */ 

require '../../common.php';
$uid = !empty($_GET['uid']) ? intval($_GET['uid']) : 0;
$page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = !empty($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
$ord = !empty($_GET['ord']) && in_array(trim($_GET['ord']), array('asc','desc')) ? trim($_GET['ord']) : 'desc';
$datatype = isset($_GET['datatype']) && in_array($_GET['datatype'], array('json','serialize','xml')) ? $_GET['datatype'] : 'json';

if(empty($uid))
{
    $warning = "uid不能为空";
    display_response($datatype, array('errno'=>-200, 'errmsg'=>$warning));
    exit;
}

$appname = !empty($_GET['appname']) ? trim($_GET['appname']) : '';

$cls_message = new cls_message();
$result = $cls_message->get_received($uid, $page, $pagesize,  $ord, $appname);
if($result)
{
    foreach($result as $val)
    {
        $fuid_arr[] = $val['uid'];
        $msgid_arr[] = $val['msg_id'];   
    }
    array_unique($uid_arr);
    array_unique($msgid_arr);
    $cls_user = new cls_user();
    $userinfo = $cls_user->get_userinfo_list($uid_arr);
    unset($uid_arr);
    $message = $cls_message->get_content_list($msg_arr);
    unset($msg_arr);
    if($userinfo)
    {
        foreach($userinfo as $val)
        {
            $sender_info[$val['uid']] = $val;
        }
    }
    unset($userinfo);
    if($message)
    {
        foreach($message as $val)
        {
            $message_list[$val['msg_id']] = $val;
        }
    }
    unset($message);
    foreach($result as $key=>$val)
    {
        $result[$key]['content'] = isset($message_list[$val['msg_id']]['content']) ? $message_list[$val['msg_id']]['content'] : '';
        $result[$key]['datetime'] = isset($message_list[$val['msg_id']]['datetime']) ? $message_list[$val['msg_id']]['datetime'] : 0;
        $result[$key]['sender'] = isset($sender_info[$val['uid']]) ? $sender_info[$val['uid']] : ''; 
        
    }
}

if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功', 'result'=>$result);
}
display_response($datatype, $response);      
