<?php

session_start();
include_once( '../common.php' );
include_once( 'config.php' );

$classname = "cls_client".TARGET;
$c = new $classname( WB_AKEY , WB_SKEY , $_SESSION['last_key']['access_token'] , $_SESSION['last_key']['access_secret']);
$ms  = $c->home_timeline(); // done


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>test</title>
</head>
<body>

<h2><?=$me['name']?> 你好~ 要换头像么?</h2>
<form action="weibolist.php" >
<input type="text" name="avatar" style="width:300px" value="头像url" />
&nbsp;<input type="submit" />
</form>
<h2>发送新微博</h2>
<form action="weibolist.php" >
<input type="text" name="text" style="width:300px" />
&nbsp;<input type="submit" />
</form>

<h2>发送图片微博</h2>
<form action="weibolist.php" >
<input type="text" name="text" style="width:300px" value="文字内容" />
<input type="text" name="pic" style="width:300px" value="图片url" />
&nbsp;<input type="submit" />
</form>
</body>
</html>
<?php

if( isset($_REQUEST['text']) || isset($_REQUEST['avatar']) )
{

if( isset($_REQUEST['pic']) )
	$rr = $c ->upload( $_REQUEST['text'] , $_REQUEST['pic'] );
elseif( isset($_REQUEST['avatar']  ) )
	$rr = $c->update_avatar( $_REQUEST['avatar'] );
else
	$rr = $c->update( $_REQUEST['text'] );	

	echo "<p>发送完成</p>" ; 

}

?>

<?php if( is_array( $ms ) ): ?>
<?php foreach( $ms as $item ): ?>
<div style="padding:10px;margin:5px;border:1px solid #ccc">
<?=$item['text'];?>
</div>
<?php endforeach; ?>
<?php endif; ?>
