<?php 
/**
 * 登录
 * @param string $username 用户名
 * @param string $pass 密码
 * @param string $ip 用户IP
 */

require '../../common.php';
$info['username'] = !empty($_POST['username']) ? trim($_POST['username']) : '';
$info['pass'] = !empty($_POST['pass']) ? trim($_POST['pass']) ? '';
$info['ip'] = !empty($_POST['ip']) ? trim($_POST['ip']) ? '';
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
$result = $cls_user->login($info['username'],$info['pass'],$info['ip']);
if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功', 'result'=>$result);
}
display_response($datatype, $response);
