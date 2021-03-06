<?php 
 /**
   * 获取用户信息
   * @param int $uid 
   * return array
   */

require '../../common.php';
$uid = !empty($_GET['uid']) ? intval($_GET['uid']) : 0;
$datatype = isset($_GET['datatype']) && in_array($_GET['datatype'], array('json','serialize','xml')) ? $_GET['datatype'] : 'json';

if(empty($uid))
{
    $warning = "uid不能为空";
    display_response($datatype, array('errno'=>-200, 'errmsg'=>$warning));
    exit;
}

$cls_user = new cls_user();
$result = $cls_user->get_userinfo($uid);

if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功', 'result'=>$result);
}
display_response($datatype, $response);