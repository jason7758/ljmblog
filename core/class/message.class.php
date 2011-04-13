<?php 
/**
　* 站内消息操作类
　*
　*/
class cls_message extends cls_pdo
{
    private $t_messages = 'messages';
    private $t_messages_relation = 'messages_relation';
    private $t_messages_forbid = 'messages_forbiden';

    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
　   * 发送消息
　   * @param array $info
     * @param int $info['uid'] 发送人用户ID
     * @param string $info['fuids'] 接收人用户ID
     * @param string $info['content'] 消息内容
     * @param string $info['appname'] 消息来自哪个应用
     * return bool
　   */            
    public function send($info)
    {
        if(empty($info['uid']) || empty($info['content']) || empty($info['appname']) || empty($info['fuids'])) return false;
        $info['uid'] = intval($info['uid']);
        $info['appname'] = mysql_real_escape_string(trim($info['appname']));
        $info['content'] = mysql_real_escape_string(trim($info['content']));
        $info['datetime'] = time();
        $msg_id = $this->insert($info, $this->t_messages, true);
        if(intval($msg_id) > 0)
        {
            $fuids = explode(',',$info['fuids']);
            $seprator = "";
            $values = " values ";
            $forbiden_list = $this->get_forbiden($info['uid']); //黑名单
            foreach($fuids as $key=>$val)
            {
                $key == 100 && break; 
                $val = intval($val);
                $val < 1 && continue;
                (is_array($forbiden_list) && in_array($val, $forbiden_list)) && continue;
                $values .= $seprator . "({$msg_id},{$info['uid']},{$val},'{$info['appname']}')";
                $seprator = ",";
            }
            $sql = "insert into ".$this->t_member_attention."(`msg_id`, `uid`, `fuid`, `appname`) $values ";
            return $this->query($sql);                  
        }
        
        return false;           
    }
    
    /**
　   * 读取一条消息内容
     * @param int $msg_id
　   * return array
　   */  
    public function get_content($msg_id)
    {
        if(empty($msg_id)) return false;
        $msg_id = intval($msg_id);
        $sql = "select * from ".$this->t_messages." where `msg_id` = $msg_id";
        return $this->fetch($sql);      
    }
    
     /**
　   * 读取多条消息内容
　   * @param array $msgid_arr
     * return array
　   */  
    public function get_content_list($msgid_arr)
    {
        if(!is_array($msg_id)) return false;
        $msgid_str = implode($msgid_arr);
        $sql = "select * from ".$this->t_messages." where `msg_id` in ({$msgid_arr})";
        return $this->fetch_all($sql);      
    }
    
    /**
　   * 读取某人收到的消息
　   * @param int $uid
     * @param string $ord
     * return array
　   */  
    public function get_received($uid, $page=1, $pagesize=10, $ord='desc', $appname=null)
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $page = $page < 1 ? 1 : intval($page);
        $pagesize = $pagesize < 1 ? 1 : intval($pagesize);
        $limit = " limit ".($page-1)*$pagesize.",".$pagesize;
        $ord = in_array($ord, array('asc','desc')) ? $ord : 'desc';
        $sql = "select * from ".$this->t_messages_relation." where `fuid` = $uid and `deleted` = 0 {$appname} {$limit} order by `msg_id` {$ord}";
        return $this->fetch_all($sql);      
    }  
    
    /**
　   * 读取某人已发送的消息
　   * @param int $uid
     * @param string $ord
     * return array
　   */  
    public function get_sended($uid, $page=1, $pagesize=10,  $ord='desc', $appname=null)
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $page = $page < 1 ? 1 : intval($page);
        $pagesize = $pagesize < 1 ? 1 : intval($pagesize);
        $limit = " limit ".($page-1)*$pagesize.",".$pagesize;
        $ord = in_array($ord, array('asc','desc')) ? $ord : 'desc';
        $sql = "select * from ".$this->t_messages_relation." where `uid` = $uid and `deleted` = 0 {$appname} {$limit} order by `msg_id` {$ord}";
        return $this->fetch_all($sql);      
    }      
    
     /**
　   * 读取某消息的所有收信人
　   * @param int $msg_id
     * @param string $ord
     * return array
　   */  
    public function get_receiver($msg_id, $page=1, $pagesize=10, $ord='desc')
    {
        if(empty($msg_id)) return false;
        $msg_id = intval($msg_id);
        $page = $page < 1 ? 1 : intval($page);
        $pagesize = $pagesize < 1 ? 1 : intval($pagesize);
        $limit = " limit ".($page-1)*$pagesize.",".$pagesize;
        $ord = in_array($ord, array('asc','desc')) ? $ord : 'desc';
        $sql = "select `fuid` from ".$this->t_messages_relation." where `msg_id` = $msg_id and `deleted` = 0 {$limit} order by `msg_id` {$ord}";
        return $this->fetch_col($sql);      
    }  
    
    /**
　   * 删除消息内容
　   * @param int $uid
     * @param int $msg_id
     * return bool
　   */  
    public function delete($uid, $msg_id)
    {
        if(empty($uid) || empty($msg_id)) return false;
        $uid = intval($uid);
        $msg_id = intval($msg_id);
        $sql = "update ".$this->t_messages_relation." set `deleted`=1 where (`uid` = $uid or `fuid` = $uid) and `msg_id` = $msg_id";
        return $this->query($sql);      
    }
    
     /**
     * 获取新消息
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_new_msg($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `msg_id` from ".$this->t_messages_relation." where `fuid` = $uid and `status` = 1 and `deleted` = 0 ".$appname;
        return $this->fetch_col($sql);
    }
    
    /**
     * 重置新消息状态
     * @param int $uid
     * @param string $appname
     * return bool
     */
    public function reset_new_msg($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "update ".$this->t_messages_relation." set `status`=0 where `fuid` = $uid and `status` = 1  and `deleted` = 0 ".$appname;
        return $this->query($sql);
    }
    

    /**
     * 加入私信黑名单（支持批量，一次最多100个）
     * @param int $uid
     * @param string $fuids
     * @param string $appname
     * return bool
     */
    public function add_forbiden($uid, $fuids, $appname) 
    {
        if(empty($uid) || empty($fuids)) return false;
        $fuids = explode(',',$fuids); 
        $uid = intval($uid);
        $appname = mysql_real_escape_string(trim($appname));
        $seprator = "";
        $values = " values ";
        foreach($fuids as $key=>$val)
        {
            $key == 100 && break; 
            $val = intval($val);
            $val < 1 && continue;
            $values .= $seprator . "($uid,$val,'$appname')";
            $seprator = ",";
        }
        $sql = "insert into ".$this->t_messages_forbiden."(`uid`, `fuid`, `appname`) $values ";
        return $this->query($sql);
        
    }
    
    /**
     * 读取某用户的私信黑名单
     * @param int $uid
     * @param string $appname
     * return array
     */
    public function get_forbiden($uid, $appname=null) 
    {
        if(empty($uid)) return false;
        $uid = intval($uid);
        $appname = $appname ? " and `appname` = '$appname' " : "";
        $sql = "select `fuid` from ".$this->t_messages_forbiden." where `uid` = $uid ".$appname;
        return $this->fetch_col($sql);
    }
    
}