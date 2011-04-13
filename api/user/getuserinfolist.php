<?php 
 /**
   * 获取用户信息（支持批量）
   * @param string $uids 多个UID用半角逗号分隔
   * return array
   */

require '../../common.php';
$uids = !empty($_GET['uids']) ? trim($_GET['uids']) : '';
$datatype = isset($_GET['datatype']) && in_array($_GET['datatype'], array('json','serialize','xml')) ? $_GET['datatype'] : 'json';

if(empty($uids))
{
    $warning = "uids不能为空";
    display_response($datatype, array('errno'=>-200, 'errmsg'=>$warning));
    exit;
}
$uids = explode(',', $uids);
foreach($uids as $key=>$val)
{
    $uids[$key] = intval($val);
}

$cls_user = new cls_user();
$result = $cls_user->get_userinfo_list($uids);
if(is_array($result))
{
    foreach($result as $val)
    {
        $list[$val['uid']] = $val;
    }
    $result = $list;
    unset($list);
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