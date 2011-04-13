<?php 
 /**
   * 修改用户信息
   * @param 
   * return 
   */

require '../../common.php';
$datatype = isset($_POST['datatype']) && in_array($_POST['datatype'], array('json','serialize','xml')) ? $_POST['datatype'] : 'json';

if(empty($_POST['uid']))
{
    $warning = "uid不能为空";
    display_response($datatype, array('errno'=>-200, 'errmsg'=>$warning));
    exit;
}

$cls_user = new cls_user();
$result = $cls_user->modify($_POST);
if($result === false)
{
    $response = array('errno'=>-5, 'errmsg'=>'内部错误，请稍后重试'); 
}
else
{
    $response = array('errno'=>1, 'errmsg'=>'成功');
}
display_response($datatype, $response);