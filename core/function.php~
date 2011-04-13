<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
function get_user_info()
{
	$user_info = array('uid' => 0, 'username' => '');
	include_once(ROOT_DIR.'/include/SSOConfig.class.php');

	$cls_check_login = new cls_check_login;
	$result = $cls_check_login->is_login();
	$result && $user_info = $result;


//	$sso = new SSOClient();
//	if($sso->isLogined())
//	{
//		$sina_user_info =$sso->getUserInfo();
//		$user_info = array(
//			'uid' => $sina_user_info['uniqueid'],
//			'username' => $sina_user_info['userid'],
//		);
//	}

	return $user_info;
}
/**
 * 转义
 *
 * @param unknown_type $string
 * @return unknown
 */
function daddslashes($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
		{
			$string[$key] = daddslashes($val);
		}
	}
	else
	{
		$string = addslashes($string);
	}
	return $string;
}
/**
 * 取消转义
 *
 * @param unknown_type $string
 * @return unknown
 */
function dstripslashes($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
		{
			$string[$key] = dstripslashes($val);
		}
	}
	else
	{
		$string = stripslashes($string);
	}
	return $string;
}
function gbk_to_utf8($arr)
{
//	if(is_string($arr))
//	{
//		$arr = mb_convert_encoding($arr, 'UTF-8', 'GBK');
//	}
//	elseif(is_array($arr))
//	{
//		eval('$arr='.mb_convert_encoding(var_export($arr,1).';', 'UTF-8', 'GBK'));
//	}
	
    if(empty($arr))
    {
        return $arr;
    }
    if(is_array($arr))
    {
        foreach($arr as $key => $value)
        {
			$key = mb_convert_encoding($key ,'UTF-8' ,'GBK');
            $arr[$key] = gbk_to_utf8($value);
        }
    }
    else
    {
        $arr = mb_convert_encoding($arr ,'UTF-8' ,'GBK');
    }
    return $arr;
}
function utf8_to_gbk($arr)
{
//	if(is_string($arr))
//	{
//		$arr = mb_convert_encoding($arr, 'GBK', 'UTF-8');
//	}
//	elseif(is_array($arr))
//	{
////		echo mb_convert_encoding(var_export($arr,1).';', 'GBK', 'UTF-8');exit;
//		eval('$arr='.mb_convert_encoding(var_export($arr,1).';', 'GBK', 'UTF-8'));
//	}
	
    if(empty($arr))
    {
        return $arr;
    }
    if(is_array($arr))
    {
        foreach($arr as $key => $value)
        {
        	$keyN = mb_convert_encoding($key ,'GBK' ,'UTF-8');
        	if($keyN != $key)  unset($arr[$key]);
            $arr[$keyN] = utf8_to_gbk($value);
        }
    }
    else
    {
    	if(!empty($arr))
        $arr = mb_convert_encoding($arr ,'GBK' ,'UTF-8');
    }
    return $arr;
}

function diff_time($time){
	$diff = TIME - $time;
	$str = '';
	
	if($diff < 60)
	{
		$str = '1分钟前';
	}
	elseif($diff < 3600)
	{
		$diff = ceil($diff/60);
		$str = $diff.'分钟前';
	}
	elseif($diff < 86400 && date("Y-m-d") == date("Y-m-d", $time))
	{
		$str = '今天'.date("H:i", $time);
	}
	else
	{
		$str = date("Y-m-d H:i", $time);
	}

	return $str;
}
function is_date($str)
{
	$pattern = '|^(\d){4}-(\d){2}-(\d){2}$|';
	return preg_match($pattern, $str);
}
/**
 * 取自织梦论坛
 *
 * @param unknown_type $str
 * @param unknown_type $ishead
 * @param unknown_type $isclose
 * @return unknown
 */
function SpGetPinyin($str,$ishead=0,$isclose=1)
{
	global $pinyins;
	$restr = '';
	$str = trim($str);
	$slen = strlen($str);
	if($slen<2)
	{
		return $str;
	}
	if(count($pinyins)==0)
	{
		$fp = fopen(ROOT_DIR.'/data/pinyin.dat','r');
		while(!feof($fp))
		{
			$line = trim(fgets($fp));
			$pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
		}
		fclose($fp);
	}
	for($i=0;$i<$slen;$i++)
	{
		if(ord($str[$i])>0x80)
		{
			$c = $str[$i].$str[$i+1];
			$i++;
			if(isset($pinyins[$c]))
			{
				if($ishead==0)
				{
					$restr .= $pinyins[$c];
				}
				else
				{
					$restr .= $pinyins[$c][0];
				}
			}else
			{
				$restr .= "_";
			}
		}else if( eregi("[a-z0-9]",$str[$i]) )
		{
			$restr .= $str[$i];
		}
		else
		{
			$restr .= "_";
		}
	}
	if($isclose==0)
	{
		unset($pinyins);
	}
	return $restr;
}

//判断是否数字
function intval_ext( $id )
{
	if( is_numeric($id))
	{
		return $id;
	}
	else{
		return 0;
	}
}
/**
 * 按字节数截取字符串(支持多字节编码)
 * @param unknown_type $string
 * @param unknown_type $length
 * @param unknown_type $code
 * @param unknown_type $etc
 */

function smarty_modifier_truncate_cn($string, $length = 80, $code = 'UTF-8', $etc = '')
{
    if( $length <= 0 )
    {
    	$string = '';
    }
    else 
    {
    	if( strlen($string) > $length )
    	{
    		$string = mb_strcut( $string, 0, $length-strlen($etc), $code ).$etc;
    	}	
    }
	return $string;
}
//判断是否为1-12位非0开头数字
function is_uid( $id )
{
	if( preg_match('/^[^0]\d{1,12}$/', $id))
	{
		return $id;
	}
	else
	{
		return 0;
	}
}


function getHeaderInfo($url, $type = 'Location'){
        $url = parse_url($url);
        if($fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$error)){
                fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n");
                fputs($fp,"Host:$url[host]\r\n\r\n");
                while(!feof($fp)){
                        $tmp = fgets($fp);
                        if(trim($tmp) == ''){
                                break;
                        }else if(preg_match("/$type:(.*)/si",$tmp,$arr)){
                                return trim($arr[1]);
                        }
                }
                return null;
        }else{
                return null;
        }
}
function is_utf8($string)
{
	// From http://w3.org/International/questions/qa-forms-utf-8.html
	return preg_match('%^(?:
	[\x09\x0A\x0D\x20-\x7E] # ASCII
	| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
	| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
	| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
	| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
	| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
	| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
	| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
	)*$%xs', $string);

}
function mb_filter($str)
{
	mb_internal_encoding("utf-8");
	$filter = "`~!#$%^&*()-=_+[]{}|\';:\"?/><,.　｀～·！◎＃￥％※×（）—＋－＝§÷】【『』‘’“”；：？、》。《，／＞＜｛｝＼";
	$filter = mb_convert_encoding($filter,"utf-8","gbk");
	$length = mb_strlen($filter);
	for($i=0;$i<$length;$i++)
	{
		$v = mb_substr($filter,$i,1);
		$str = mb_str_ireplace($v,' ',$str);
	}
	return $str;
}

function mb_str_ireplace($search, $replace, $content)
{
		$offset = 0;
		$contentlen = mb_strlen($content);
		$count = 0;
		while(($poz = mb_strpos($content, $search, $offset)) !== false)
		{
			if(++$count>$contentlen) break;
			$offset = $poz + mb_strlen($replace);
			$content = mb_substr($content, 0, $poz). $replace .mb_substr($content, $poz+mb_strlen($search));
		}
		return $content;
}


function get_ip() 
{
    if(getenv('HTTP_CLIENT_IP'))  
    {  
            $ip = getenv('HTTP_CLIENT_IP');  
    }  
    elseif(getenv('HTTP_X_FORWARDED_FOR'))  
    {  
           $ip = getenv('HTTP_X_FORWARDED_FOR');  
    }  
    elseif(getenv('REMOTE_ADDR'))  
    {  
            $ip = getenv('REMOTE_ADDR');  
    }  
    else  
    {  
           $ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];  
    } 
    return $ip;
}

function display_response($data_type, $data)
{
    switch ($data_type)
    {
        case 'json':
            echo empty($_REQUEST['callback']) ? json_encode($data) : $_REQUEST['callback'] . "(" . json_encode($data) . ")";
            break;
        case 'serialize':
            echo serialize($data);
            break;
        case 'xml':
            header('Content-Type: text/xml');
            $ax = new cls_array2xml(array('list' => $data), 'utf-8');
            header('Content-Type: text/xml');
            echo $data = $ax->getXML();
            break;
    }
}

function get_request_token($target, $app_key , $app_secret, $callback)
{
    $classname = 'cls_oauth'.$target;
    $oauth = new $classname($app_key , $app_secret);
    $keys = $oauth->getRequestToken($callback);
    $result = array('request_token' =>$keys['oauth_token'],'request_secret'=>$keys['oauth_token_secret']);
    $result['request_url'] = $oauth->getAuthorizeURL($result['request_token'] ,false , $callback); 
    return $result;        
}

function get_access_token($target, $app_key , $app_secret, $request_token, $request_secret)
{  
    $classname = 'cls_oauth'.$target;
    $oauth_verifier = $target == 'sohu' ?  $_REQUEST['oauth_token'] : $_REQUEST['oauth_verifier'];
    $oauth = new $classname($app_key , $app_secret, $request_token, $request_secret);
    $keys = $oauth->getAccessToken($oauth_verifier) ;
    $result = array('access_token' =>$keys['oauth_token'],'access_secret'=>$keys['oauth_token_secret']);
    return $result;
}