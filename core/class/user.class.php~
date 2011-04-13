<?php 
/**
　* 用户操作类
　*
　*/
class cls_user extends cls_pdo
{
    private $t_members = 'members';
    private $t_member_attention = 'member_attention';
    private $t_member_source = 'member_source';
    private $t_member_credits = 'member_credits';
    private $t_member_credits_log = 'member_credits_log';
    private $t_member_friends = 'member_friends';
    private $t_member_friend_request = 'member_friend_request';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 登录
     * @param string $username 用户名
     * @param string $pass 密码
     * @param string $ip 用户IP
     */
     public function login($username,$pass,$ip)
     {
        if(empty($username) || empty($pass)) return false;
        $where = " where `status` = 0 and `pass` = '".mysql_real_escape_string($pass)."' and ";
        if(is_int($username))
        {
            $where .= " `uid` = ".intval($username);
        }
        elseif(strpos($username,'@'))
        {
            $where .= " `email` = '".mysql_real_escape_string(trim($username))."'";                
        }
        else 
        {
            $where .= " `nick` = '".mysql_real_escape_string(trim($username))."'";        
        }
        
        $sql = "select * from ".$this->t_members.$where;
        $userinfo = $this->fetch($sql);
        $this->modify($uid, array('lastip'=>$ip, 'lastvisit'=>time()));  
        return $userinfo;
        
     } 
    
    /**
     * 用微博账号登录
     * @param array $info
     * @param int $info['suid'] 微博UID
     * @param string $info['source'] 来自哪个微博
     * @param string $info['nick'] 用户昵称
     * @param string $info['portrait'] 用户头像
     * @param int $info['meattnum'] 关注人数
     * @param int $info['attmenum'] 粉丝人数
     * @param int $info['gender'] 性别
     * @param string $info['country'] 国家
     * @param string $info['privince'] 省份
     * @param string $info['city'] 城市
     * @param string $info['ip'] 用户IP地址
     * return array or bool
     */
    public function checkin($info) 
    {
        if(empty($info['suid']) || empty($info['source'])) return false; 
        $info['suid'] = intval($info['suid']);
        $info['source'] = trim($info['source']);
        $datetime = time();
        $uid = $this->get_uid_by_suid($info['suid'], $info['source']);
        if(intval($uid) > 0)
        {
            $userinfo = $this->get_userinfo($uid); 
            $this->modify($uid, array('lastip'=>$info['ip'], 'lastvisit'=>$datetime));  
            return $userinfo;
        }
        else 
        {
            $info['regdate'] = $datetime;
            $info['regip'] = $info['ip'];
            unset($info['ip']);
            $uid = $this->insert($info, $this->t_members,true); 
            if($uid)
            {
                $this->init_credits($uid);
                $this->insert(array(`uid`=>$uid,'suid'=>$info['suid'], 'nick'=>$info['nick']), $this->t_member_source, true); 
                $userinfo = $this->get_userinfo($uid); 
                $this->modify($uid, array('lastip'=>$info['ip'], 'lastvisit'=>$datetime));  
                return $userinfo;   
            }  

            return false;    
        }
    }
    
    /**
     * 通过微博用户ID（SUID）获取UID
     * @param int $suid 微博UID
     * @param string $source 来自哪个微博
     * return int
     */
    public function get_uid_by_suid($suid, $source) 
    {
        if(empty($suid) || empty($source)) return false; 
        $suid = intval($suid);
        $source = trim($source);
        $sql = "select `uid` from ".$this->t_member_source." where `suid` = {$suid} and `source` = '{$source}'";
        return $this->fetch_one($sql);
    }
    
    /**
     * 获取一条用户信息
     * @param int $uid
     * return array
     */
    public function get_userinfo($uid) 
    {
        $uid =intval($uid);
        $sql = "select * from ".$this->t_members." where `uid` = {$uid}";
        return  $this->fetch($sql);
    }    

    /**
     * 获取多条用户信息
     * @param array $uid_arr
     * return array
     */
    public function get_userinfo_list($uid_arr) 
    {
        if(!is_array($uid_arr)) return false;
        $uid_str = implode(',', $uid_arr);
        $sql = "select * from ".$this->t_members." where `uid` in ({$uid_str})";
        return  $this->fetch_all($sql);
    }        

    /**
     * 根据微博UID判断是否是新用户
     * @param int $suid 微博用户UID 
     * @param string $source 来自哪个微博（sina,163,qq,sohu）
     * return int
     */
    public function is_newcomer($suid, $source) 
    {
        if(empty($suid) || empty($source)) return false; 
        $suid = intval($suid);
        $source = trim($source);
        $sql = "select `uid` from ".$this->t_member_source." where `suid` = {$suid} and `source` = '{$source}'";
        return  $this->fetch_one($sql);
        
    }
    
    /**
     * 根据用户昵称获取用户ID
     * @param string $nick
     * return int
     */
    public function get_uid_by_nick($nick) 
    {
        $nick = mysql_real_escape_string(trim($nick));
        $sql = "select `uid` from ".$this->t_members." where `nick` = '{$nick}'";
        return  $this->fetch_one($sql);
    }
    
     /**
     * 根据用户ID获取用户昵称
     * @param int $uid
     * return string
     */
    public function get_nick_by_uid($uid) 
    {
        $uid =intval($uid);
        $sql = "select `nick` from ".$this->t_members." where `uid` = {$uid}";
        return  $this->fetch_one($sql);
    }
    
    /**
     * 修改用户信息
     * @param int $uid
     * @param array $info
     * return bool 
     */
    public function modify($info) 
    {
        if(!$info['uid'] || !is_array($info)) return false;
        $uid = intval($info['uid']);
        unset($info['uid']);
        foreach($info as $key=>$val)
        {
            if(is_int($val))
            {
                $info[$key] = intval($val);           
            }        
            else
            {
                $info[$key] =  mysql_real_escape_string(trim($val));            
            }
        }
        return $this->update($info, $this->t_members, " where `uid` = $uid ", true);
        
    }
    
    /**
     * 验证用户信息(昵称或EMAIL等)是否已经存在
     * @param array $info
     * return int 
     */
    public function check_userinfo($info) 
    {
        if(!is_array($info)) return false;
        $key = current(array_keys($info));
        $value = current(array_values($info));
        $sql = "select `uid` from ".$this->t_members." where `{$key}` = '{$value}'";
        return  $this->fetch_one($sql);
        
    }
    
    /**
     * 添加关注人（支持批量，一次最多100个）
     * @param int $uid
     * @param string $fuids 
     * @param string $appname
     * return bool
     */
    public function add_attention($uid, $fuids, $appname) 
    {
        if(empty($uid) || empty($fuids)) return false;
        $fuids = explode(',',$fuids); 
        $uid = intval($uid);
        $time = time();
        $appname = mysql_real_escape_string(trim($appname));
        $seprator = "";
        $values = " values ";
        foreach($fuids as $key=>$val)
        {
            $key == 100 && break; 
            $val = intval($val);
            $val < 1 && continue;
            $values .= $seprator . "($uid,$val,'$appname', $time)";
            $seprator = ",";
        }
        $sql = "insert into ".$this->t_member_attention."(`uid`, `fuid`, `appname`, `datetime`) $values ";
        return $this->query($sql);
        
    }
    
     /**
     * 添加好友（支持批量，一次最多100个）
     * @param int $uid
     * @param string $fuids 
     * @param string $appname
     * return bool
     */
    public function add_friends($uid, $fuids, $appname) 
    {
        if(empty($uid) || empty($fuids)) return false;
        $fuids = explode(',',$fuids);
        $uid = intval($uid);
        $time = time();
        $appname = mysql_real_escape_string(trim($appname));
        $seprator = "";
        $values = " values ";
        foreach($fuids as $key=>$val)
        {
            $key == 100 && break; 
            $val = intval($val);
            $val < 1 && continue;
            $values .= $seprator . "($uid,$val,'$appname',$time)";
            $seprator = ",";
        }
        $sql = "insert into ".$this->t_member_friends."(`uid`, `fuid`, `appname`, `datetime`) $values ";
        return $this->query($sql);
        
    }
    
     /**
     * 添加好友请求（支持批量，一次最多100个）
     * @param int $uid
     * @param string $fuids 
     * @param string $appname
     * return bool
     */
    public function add_friend_request($uid, $fuids, $appname) 
    {
        if(empty($uid) || empty($fuids)) return false;
        $fuids = explode(',',$fuids);
        $uid = intval($uid);
        $time = time();
        $appname = mysql_real_escape_string(trim($appname));
        $seprator = "";
        $values = " values ";
        foreach($fuids as $key=>$val)
        {
            $key == 100 && break; 
            $val = intval($val);
            $val < 1 && continue;
            $values .= $seprator . "($uid,$val,'$appname',$time)";
            $seprator = ",";
        }
        $sql = "insert into ".$this->t_member_friend_request."(`uid`, `fuid`, `appname`, `datetime`) $values ";
        return $this->query($sql);
        
    }
    /**
     * 获取我关注的人
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_meatt($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `fuid` from ".$this->t_member_attention." where `uid` = $uid ".$appname;
        return $this->fetch_col($sql);
    }
    
    /**
     * 获取我的粉丝
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_attme($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `uid` from ".$this->t_member_attention." where `fuid` = $uid ".$appname;
        return $this->fetch_col($sql);
    }
    
    /**
     * 获取我的好友
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_friends($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `fuid` from ".$this->t_member_friends." where `uid` = $uid ".$appname;
        return $this->fetch_col($sql);
    }
    
    /**
     * 获取新增好友请求
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_new_friend_request($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `uid` from ".$this->t_member_friend_request." where `fuid` = $uid and `status` = 1 ".$appname;
        return $this->fetch_col($sql);
    }
    
    /**
     * 重置新增好友请求
     * @param int $uid
     * @param string $appname
     * return bool
     */
    public function reset_new_friend_request($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "update ".$this->t_member_friend_request." set `status`=0 where `fuid` = $uid and `status` = 1 ".$appname;
        return $this->query($sql);
    }
    
     /**
     * 获取新增粉丝
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_new_attme($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `uid` from ".$this->t_member_attention." where `fuid` = $uid and `status` = 1 ".$appname;
        return $this->fetch_col($sql);
    }
    
    /**
     * 重置新增粉丝
     * @param int $uid
     * @param string $appname
     * return bool
     */
    public function reset_new_attme($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "update ".$this->t_member_attention." set `status`=0 where `fuid` = $uid and `status` = 1 ".$appname;
        return $this->query($sql);
    }
    
     /**
     * 冻结账户
     * @param int $uid
     * return bool
     */
    public function frozen($uid) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $sql = "update ".$this->t_members." set `status`=1 where `uid` = $uid ";
        return $this->query($sql);
    }
    
    /**
     * 解除冻结账户
     * @param int $uid
     * return bool
     */
    public function unfrozen($uid) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $sql = "update ".$this->t_members." set `status`=0 where `uid` = $uid ";
        return $this->query($sql);
    }
    
    /**
     * 初始化用户积分
     * @param int $uid
     * return bool
     */
    public function init_credits($uid) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        return $this->insert(array('uid'=>$uid), $this->t_member_credits);
    }
    
    /**
     * 获取用户积分
     * @param int $uid
     * return array
     */
    public function get_credits($uid) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $sql = "select * from ".$this->t_member_credits." where `uid` = $uid ";
        return $this->fetch($sql);
    }
    
    /**
     * 获取多个用户的积分
     * @param array $uid_arr
     * return array
     */
    public function get_credits_list($uid_arr) 
    {
        if(!is_array($uid_arr)) return false;
        $uid_str = implode(',', $uid_arr);
        $sql = "select * from ".$this->t_members_credits." where `uid` in ({$uid_str})";
        return  $this->fetch_all($sql);
    }
    
    /**
     * 更新用户积分
     * @param int $uid
     * @param int $credits
     * @param string $field
     * return bool
     */
    public function update_credits($info) 
    {
        if(empty($info['uid']) || empty($info['credits']) || empty($info['appname']))  return false;
        $info['uid'] = intval($info['uid']);
        $info['credits'] = intval($info['credits']);
        $info['field'] = $info['field'] ? $info['field'] : 'credits';
        $sql = "update ".$this->t_member_credits." set `{$info['field']}`= `{$info['field']}` + {$info['credits']} where `uid` = {$info['uid']} ";
        $result =  $this->query($sql);
        if($result)
        ｛
            $this->add_credits_log($info);
        ｝
    }
    
     /**
     * 记录积分日志
     * @param array $info
     * @param int $info['uid']
     * @param int $info['credits']
     * @param string $info['field']
     * @param string $info['appname']
     * @param int $info['datetime']
     * @param string $info['remark']
     * return bool
     */
    public function add_credits_log($info) 
    {
        if(empty($info['uid']) || empty($info['credits']) || empty($info['appname'])) return false;
        $info['uid'] = intval($info['uid']);
        $info['credits'] = intval($info['credits']);
        $info['field'] = $info['field'] ? $info['field'] : 'credits';
        $info['datetime'] = time();
        return $this->insert($info, $this->t_member_credits_log, true);
    }
    
    
    
}