<?php 
 /**
   * 更新用户积分
   * @param int $uid 
   * @param int $credits
   * return array
   */

require '../../common.php';
$info['uid'] = !empty($_POST['uid']) ? intval($_POST['uid']) : 0;
$info['credits'] = !empty($_POST['credits']) ? intval($_POST['credits']) : 0;
$info['appname'] = !empty($_POST['appname']) ? trim($_POST['appname']) : '';
$info['field'] = !empty($_POST['field']) ? trim($_POST['field']) : 'credits';
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

$info['remark'] = !empty($_POST['remark']) ? trim($_POST['remark']) : '';

$cls_user = new cls_user();
$result = $cls_user->update_credits($info);

if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功');
}
display_response($datatype, $response);