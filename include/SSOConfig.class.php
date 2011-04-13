<?PHP
/**
 * Sina sso client config file
 * @package  SSOClient
 * @filename SSOConfig.class.php
 * @author   lijunjie <junjie2@staff.sina.com.cn>
 * @date 	 2009-05-05
 * @version  1.1
 */

require_once('SSOCookie.php');
require_once('SSOClient.php');
class SSOConfig {
    const SERVICE 	= "supports_house"; 	//服务名称，产品名称，应该和entry保持一致
    const ENTRY 	= "supports_house";	//应用产品entry 和 pin , 获取用户详细信息使用，由统一注册颁发的
    const PIN 		= "3b6c8189d651f6bb5649537d1e7b205e";
    const COOKIE_DOMAIN = ".sina.com.cn";  //domain of cookie, 您域名所在的根域，如“.sina.com.cn”，“.51uc.com”
    const USE_SERVICE_TICKET = false; // 如果只需要根据sina.com.cn域的cookie就可以信任用户身份的话，可以设置为false，这样不需要验证service ticket，省一次http的调用
}

/**
 * 验证新浪乐居登录状态类
 * @version : 1.0
 */

class cls_check_login
{
	const IHOUSE_KEY = 'nn33DSQgqMd32CZo';//会员中心私钥
	
	/**
	 * 检查本地COOKIE
	 * return array()
	 */
	public function is_login()
	{
		$user = array();
		//验证sina cookie登录状态
		$ssoclient = new SSOClient();
		if ($ssoclient->isLogined())
		{
			$arrUserInfo = $ssoclient->getUserInfo();
			$user['uid'] = $arrUserInfo['uniqueid'];
			$user['username'] = $arrUserInfo['userid'];
			return $user;
		}
		
		
		
		//检查本地cookie  
		$chk_re = $this->check_cookie();
		$leju_cookies = !empty($chk_re['cookies'])?$chk_re['cookies']:''; 
		//echo $leju_cookies;
		if(!empty($leju_cookies))
		{
			parse_str($leju_cookies);
			if( !empty($leju_cookies) && $chk_re['result']=='succ'&& $f!=1 &&  $f!=2 && is_numeric($f) && ($f>=10001 && $f<=20000) )
			{
				$user['uid'] = $uid;
				$user['username'] = $loginname;
				return $user;
			}
			//sina cookie验证失败 清除leju cookie
			setcookie('LUP','',time()-86400,'/','.sina.com.cn');
			setcookie('LUE','',time()-86400,'/','.sina.com.cn');
			echo '<script charset="utf-8" src=" http://baidu.my.leju.com/api/cookie/cleancookie.php"></script>'; //百度乐居退出接口
			echo '<script charset="utf-8" src=" http://house.baidu.com/user/cleancookie.php"></script>'; //百度乐居退出接口
			echo '<script charset="utf-8" src=" http://udcenter.fangyou.com/ucenter/unsetcookie"></script>'; //房友退出接口
			echo '<script charset="utf-8" src=" http://esf.baidu.com/api/baiduesf.php?act=logout"></script>'; //房友退出接口

		}
		return $user;
		
		
	}
	/**
	 * 检查本地COOKIE
	 * return array (result=succ/fail ,reason=xxx, cookies=xxx)
	 */
	public function check_cookie()
	{
		$result = array ('result'=>'fail');
		if(empty($_COOKIE['LUP']) || empty($_COOKIE['LUE']))
		{
			$result['reason']='参数错误';
			return $result;
		}
		$lup = $_COOKIE['LUP'];//明文cookie
		$lue = $_COOKIE['LUE'];//加密cookie
		if(md5($lup.self::IHOUSE_KEY)!=$lue)
		{
			$result['reason']='cookie错误';
			return $result;
		}
		$result['cookies']=$lup;
		$result['result']='succ';
		return $result;
	}
}
