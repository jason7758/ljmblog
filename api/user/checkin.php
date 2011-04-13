<?php 
/**
 * 用微博账号登录
 * @param int $_POST['suid'] 微博UID
 * @param string $_POST['source'] 来自哪个微博
 * @param string $_POST['nick'] 用户昵称
 * @param string $_POST['portrait'] 用户头像
 * @param int $_POST['meattnum'] 关注人数
 * @param int $_POST['attmenum'] 粉丝人数
 * @param int $_POST['gender'] 性别
 * @param string $_POST['country'] 国家
 * @param string $_POST['privince'] 省份
 * @param string $_POST['city'] 城市
 * @param string $_POST['ip'] 用户IP地址
 * return array
 */
require '../../common.php';
$info['suid'] = !empty($_POST['suid']) ? intval($_POST['suid']) : 0;
$info['source'] = !empty($_POST['source']) ? trim($_POST['source']) : '';
$info['nick'] = !empty($_POST['nick']) ? trim($_POST['nick']) : '';
$info['portrait'] = !empty($_POST['portrait']) ? trim($_POST['portrait']) : '';
$info['meattnum'] = !empty($_POST['meattnum']) ? intval($_POST['meattnum']) : 0;
$info['attmenum'] = !empty($_POST['attmenum']) ? intval($_POST['attmenum']) : 0;
$info['gender'] = !empty($_POST['gender']) ? intval($_POST['gender']) : 0;
$info['country'] = !empty($_POST['country']) ? trim($_POST['country']) : '';
$info['province'] = !empty($_POST['province']) ? trim($_POST['province']) : '';
$info['city'] = !empty($_POST['city']) ? trim($_POST['city']) : '';
$info['ip'] = !empty($_POST['ip']) ? trim($_POST['ip']) : '';
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

$cls_user = new cls_user();
$result = $cls_user->checkin($info);
if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功', 'result'=>$result);
}
display_response($datatype, $response);
