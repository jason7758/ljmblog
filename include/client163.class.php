<?php 
/** 
 * 网易微博操作类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class cls_client163 
{ 
    /** 
     * 构造函数 
     *  
     * @access public 
     * @param mixed $akey 微博开放平台应用APP KEY 
     * @param mixed $skey 微博开放平台应用APP SECRET 
     * @param mixed $accecss_token OAuth认证返回的token 
     * @param mixed $accecss_token_secret OAuth认证返回的token secret 
     * @return void 
     */ 
    function __construct( $akey , $skey , $accecss_token , $accecss_token_secret ) 
    { 
        $this->oauth = new cls_oauth163( $akey , $skey , $accecss_token , $accecss_token_secret ); 
    } 

    /** 
     * 最新公共微博 
     *  
     * @access public 
     * @return array 
     */ 
    function public_timeline($count=20) 
    { 
        return $this->oauth->get('http://api.t.163.com/statuses/public_timeline.json'); 
    } 

    /** 
     * 最新关注人微博 
     *  
     * @access public 
     * @return array 
     */ 
    function friends_timeline() 
    { 
        return $this->home_timeline(); 
    } 

    /** 
     * 最新关注人微博 
     *  
     * @access public 
     * @return array 
     */ 
    function home_timeline($count = 20 ,$sinace = false,$max=false,$trim=false ) 
    { 
        return $this->request_163('http://api.t.163.com/statuses/home_timeline.json',  $count,$sinace ,$max,$trim); 
    } 

    /** 
     * 最新 @用户的 
     *  
     * @access public 
     * @param int $count 每次返回的最大记录数（即页面大小），不大于200，默认为30。 
     * @return array 
     */ 
    function mentions( $count = 30 ,$sinace = false,$max=false,$trim=false ) 
    { 
        return $this->request_163( 'http://api.t.163.com/statuses/mentions.json' ,  $count,$sinace ,$max,$trim ); 
    } 


    /** 
     * 发表微博 
     *  
     * @access public 
     * @param mixed $text 要更新的微博信息。 
     * @return array 
     */ 
    function update( $text,$source ="" ) 
    { 
        //  http://api.t.163.com/statuses/update.json 
        $param = array(); 
        $param['status'] = $text; 
		$param['source'] = $source; 

        return $this->oauth->post( 'http://api.t.163.com/statuses/update.json' , $param ); 
    }
    
    /** 
     * 发表图片微博 
     *  
     * @access public 
     * @param string $text 要更新的微博信息。 
     * @param string $text 要发布的图片路径,支持url。[只支持png/jpg/gif三种格式,增加格式请修改get_image_mime方法] 
     * @return array 
     */ 
    function upload( $text, $pic_path,$source =""  ) 
    { 
      $param = array(); 
      $param['pic'] = (strpos("http://",$pic_path) ? '@' : '').$pic_path;
	  $pic = $this->oauth->post( 'http://api.t.163.com/statuses/upload.json' , $param , true );
      $param1 = array(); 
      $param1['status'] = $text." ".$pic['upload_image_url']; 
	  $param1['source'] = $source; 
       return $this->oauth->post( 'http://api.t.163.com/statuses/update.json' , $param1 );   
    } 

    /** 
     * 获取单条微博 
     *  
     * @access public 
     * @param mixed $sid 要获取已发表的微博ID 
     * @return array 
     */ 

    function show_status( $sid ) 
    { 
        return $this->oauth->get( 'http://api.t.163.com/statuses/show/' . $sid . '.json' ); 
    } 


    /** 
     * 返回当前登录用户未读的新消息数量。
     *  
     * @return array 
     */ 

	    function latest() 
    { 
        return $this->oauth->get( 'http://api.t.163.com/reminds/message/latest.json' ); 
    } 
    /** 
     * 删除微博 
     *  
     * @access public 
     * @param mixed $sid 要删除的微博ID 
     * @return array 
     */ 
    function delete( $sid ) 
    { 
        return $this->destroy( $sid ); 
    } 

    /** 
     * 删除微博 
     *  
     * @access public 
     * @param mixed $sid 要删除的微博ID 
     * @return array 
     */ 
    function destroy( $sid ) 
    { 
        return $this->oauth->post( 'http://api.t.163.com/statuses/destroy/' . $sid . '.json' ); 
    } 
   /** 
     * 被谁转发过 
     *  
     * @access public 
     * @param mixed $sid 要查询的微博ID 
     * @return array 
     */ 
	    function retweeted_by( $sid,$count=false ) 
    { 
		  $param = array(); 
        if($count)$param['count'] = $count; 
        return $this->oauth->get( 'http://api.t.163.com/statuses/' . $sid . '/retweeted_by.json' ); 
    } 

    /** 
     * 个人资料 
     *  
     * @access public 
     * @param mixed $uid_or_name 用户UID或微博昵称。 
     * @return array 
     */ 
    function show_user_id( $id_or_screen_name) 
    { 
		  $param = array(); 
        $param['id'] = $id_or_screen_name; 
        return $this->oauth->get( 'http://api.t.163.com/users/show.json', $param); 
    } 

	   function show_user_name( $name) 
    { 
		  $param = array(); 
        $param['name'] = $name; 
        return $this->oauth->get( 'http://api.t.163.com/users/show.json', $param); 
    } 

    /** 
     * 关注人列表 
     *  
     * @access public 
     * @param bool $cursor 单页只能包含100个关注列表，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多的关注列表 
     * @param bool $count 每次返回的最大记录数（即页面大小），不大于200,默认返回20 
     * @param mixed $uid_or_name 要获取的 UID或微博昵称 
     * @return array 
     */ 
    function friends($uid_or_screen_name, $cursor = false ) 
    { 
        return $this->request_with_uid( 'http://api.t.163.com/statuses/friends.json' ,  $uid_or_screen_name , false , false , $cursor ); 
    } 

    /** 
     * 粉丝列表 
     *  
     * @access public 
     * @param bool $cursor 单页只能包含100个粉丝列表，为了获取更多则cursor默认从-1开始，通过增加或减少cursor来获取更多的粉丝列表 
     * @param bool $count 每次返回的最大记录数（即页面大小），不大于200,默认返回20。 
     * @param mixed $uid_or_name  要获取的 UID或微博昵称 
     * @return array 
     */ 
    function followers( $uid_or_screen_name ,$cursor = false  ) 
    { 
        return $this->request_with_uid( 'http://api.t.163.com/statuses/followers.json' ,  $uid_or_screen_name , false , false , $cursor ); 
    } 

    /** 
     * 关注一个用户 
     *  
     * @access public 
     * @param mixed $uid_or_name 要关注的用户UID或个性网址
     * @return array 
     */ 
    function follow( $uid_or_screen_name ) 
    { 
        return $this->request_with_uid( 'http://api.t.163.com/friendships/create.json' ,  $uid_or_screen_name ,  false , false , false , true  ); 
    } 
    function create( $uid_or_screen_name ) 
    { 
        return $this->request_with_uid( 'http://api.t.163.com/friendships/create.json' ,  $uid_or_screen_name ,  false , false , false , true  ); 
    } 

    /** 
     * 取消关注某用户 
     *  
     * @access public 
     * @param mixed $uid_or_name 要取消关注的用户UID或个性网址
     * @return array 
     */ 
    function unfollow( $uid_or_screen_name  ) 
    { 
        return $this->request_with_uid( 'http://api.t.163.com/friendships/destroy.json' ,  $uid_or_screen_name  ,  false , false , false , true); 
    } 
    function destroy_friend( $uid_or_screen_name  ) 
    { 
        return $this->request_with_uid( 'http://api.t.163.com/friendships/destroy.json' ,  $uid_or_screen_name  ,  false , false , false , true); 
    } 

    /** 
     * 返回两个用户关系的详细情况 
     *  
     * @access public 
     * @param mixed $uid_or_name 要判断的用户UID 
     * @return array 
     */ 
    function is_followed( $s_id_or_screen_name,$t_id_or_screen_name=false ) 
    { 
        $param = array(); 
        if( is_numeric( $s_id_or_screen_name ) ) $param['source_id'] = $s_id_or_screen_name; 
        else $param['source_screen_name'] = $s_id_or_screen_name; 
		if($t_id_or_screen_name){
        if( is_numeric( $t_id_or_screen_name ) ) $param['target_id'] = $t_id_or_screen_name; 
        else $param['target_screen_name'] = $t_id_or_screen_name; }

        return $this->oauth->get( 'http://api.t.163.com/friendships/show.json' , $param ); 
    } 

	function top_hot($type,$size=50)
	{
	  switch ($type) { 
       case "1": 
       case "oneHour":
            $lx="oneHour"; 
            break; 
       case "2": 
       case "sixHours":
            $lx="sixHours"; 
            break;  
       case "3": 
       case "oneDay":
            $lx="oneDay"; 
            break;  
       case "4": 
       case "oneWeek":
            $lx="oneWeek"; 
            break; 
   default: 
    $lx=="oneHour"; 
    break;
	  }
	
  return $this->oauth->get( 'http://api.t.163.com/statuses/topRetweets/' . $lx . '.json?size='.$size ); 


	}

    /** 
     * 用户发表微博列表 
     *  
     * @access public 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认30。 
     * @param mixed $uid_or_name 指定用户UID或微博昵称 
     * @return array 
     */ 
    function user_timeline_uid( $user_id , $count = 30 ,$since_id = false , $max_id = false , $trim_user = false ) 
    { 
        $param = array(); 
        $param['user_id'] = $user_id; 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
		if( $max_id) $param['max_id'] = $max_id; 
		if( $trim_user) $param['trim_user'] = $trim_user; 
        $url = 'http://api.t.163.com/statuses/user_timeline.json';
        return $this->oauth->get($url , $param ); 
    } 
   function user_timeline_sname( $sname , $count = 30 ,$since_id = false , $max_id = false , $trim_user = false ) 
    { 
        $param = array(); 
        $param['screen_name'] = $sname; 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
		if( $max_id) $param['max_id'] = $max_id; 
		if( $trim_user) $param['trim_user'] = $trim_user; 
		 $url = 'http://api.t.163.com/statuses/user_timeline.json';
        return $this->oauth->get($url , $param ); 
    } 
   function user_timeline_name( $name , $count = 30 ,$since_id = false , $max_id = false , $trim_user = false ) 
    { 
        $param = array(); 
        $param['name'] = $name; 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
		if( $max_id) $param['max_id'] = $max_id; 
		if( $trim_user) $param['trim_user'] = $trim_user; 
		 $url = 'http://api.t.163.com/statuses/user_timeline.json';
        return $this->oauth->get($url , $param ); 
    } 


   function retweets_of_me($count = 30 ,$since_id = false) 
    { 
        $param = array(); 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
		 $url = 'http://api.t.163.com/statuses/retweets_of_me.json';
        return $this->oauth->get($url , $param ); 
    } 

    /** 
     * 获取私信列表 
     *  
     * @access public 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认30。 
     * @return array 
     */ 
    function list_dm($count = 30 ,$since_id = false ) 
    { 

		$param = array(); 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
        return $this->oauth->get( 'http://api.t.163.com/direct_messages.json' , $param ); 
    } 

    /** 
     * 发送的私信列表 
     *  
     * @access public 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认30。 
     * @return array 
     */ 
    function list_dm_sent( $count = 30 ,$since_id = false ) 
    { 
		$param = array(); 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
        return $this->oauth->get( 'http://api.t.163.com/direct_messages/sent.json' , $param ); 
    } 

    /** 
     * 发送私信 
     *  
     * @access public 
     * @param mixed $uid_or_name UID或微博昵称 
     * @param mixed $text 要发生的消息内容，文本大小必须小于300个汉字。 
     * @return array 
     */ 
    function send_dm( $name , $text ) 
    { 
        $param = array(); 
        $param['text'] = $text; 
        $param['name'] = $name; 

        return $this->oauth->post( 'http://api.t.163.com/direct_messages/new.json' , $param  ); 
    } 

    /** 
     * 删除一条私信 
     *  
     * @access public 
     * @param mixed $did 要删除的私信主键ID 
     * @return array 
     */ 
    function delete_dm( $did ) 
    { 
        return $this->oauth->post( 'http://api.t.163.com/direct_messages/destroy/' . $did . '.json' ); 
    } 

    /** 
     * 转发一条微博信息。 
     *  
     * @access public 
     * @param mixed $sid 转发的微博ID 
     * @return array 
     */ 
    function retweet( $sid ) 
    { 
       // $param = array(); 
       // $param['id'] = $sid; 
        return $this->oauth->post( 'http://api.t.163.com/statuses/retweet/'.$sid.'.json'  ); 
    } 

    /** 
     * 对一条微博信息进行评论 
     *  
     * @access public 
     * @param mixed $sid 要评论的微博id 
     * @param mixed $text 评论内容 
     * @param bool $cid 要评论的评论id 
     * @return array 
     */ 
    function send_comment( $sid , $text , $cid = false ) 
    { 
        $param = array(); 
        $param['id'] = $sid; 
        $param['comment'] = $text; 
        if( $cid ) $param['cid '] = $cid; 

        return $this->oauth->post( 'http://api.t.163.com/statuses/comment.json' , $param  ); 

    } 

    /** 
     * 发出的评论 
     *  
     * @access public 
     * @param int $page 页码 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认20。 
     * @return array 
     */ 
    function comments_by_me( $page = 1 , $count = 20 ) 
    { 
        return $this->request_with_pager( 'http://api.t.163.com/statuses/comments_by_me.json' , $page , $count ); 
    } 

    /** 
     * 最新评论(按时间) 
     *  
     * @access public 
     * @param int $page 页码 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认20。 
     * @return array 
     */ 
    function comments_timeline( $page = 1 , $count = 20 ) 
    { 
        return $this->request_with_pager( 'http://api.t.163.com/statuses/comments_timeline.json' , $page , $count ); 
    } 

    /** 
     * 单条评论列表(按微博) 
     *  
     * @access public 
     * @param mixed $sid 指定的微博ID 
     * @param int $page 页码 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认20。 
     * @return array 
     */ 
    function get_comments_by_sid( $sid , $count = 30 ,$since_id = false , $max_id = false , $trim_user = false ) 
    { 
        $param = array(); 
        $param['id'] = $sid; 
        if( $count) $param['count'] = $count; 
		if( $since_id) $param['$sinace_id'] = $since_id; 
		if( $max_id) $param['max_id'] = $max_id; 
		if( $trim_user) $param['trim_user'] = $trim_user; 

        return $this->oauth->get('http://api.t.163.com/statuses/comments.json' , $param ); 

    } 

    /** 
     * 批量统计微博的评论数，转发数，一次请求最多获取100个。 
     *  
     * @access public 
     * @param mixed $sids 微博ID号列表，用逗号隔开 
     * @return array 
     */ 
    function get_count_info_by_ids( $sids ) 
    { 
        $param = array(); 
        $param['ids'] = $sids; 

        return $this->oauth->get( 'http://api.t.163.com/statuses/counts.json' , $param ); 
    } 

    /** 
     * 对一条微博评论信息进行回复。 
     *  
     * @access public 
     * @param mixed $sid 微博id 
     * @param mixed $text 评论内容。 
     * @param mixed $cid 评论id 
     * @return array 
     */ 
    function reply( $sid , $text , $cid ) 
    { 
        $param = array(); 
        $param['id'] = $sid; 
        $param['comment'] = $text; 
        $param['cid '] = $cid; 

        return $this->oauth->post( 'http://api.t.163.com/statuses/reply.json' , $param  ); 

    } 

    /** 
     * 返回用户的发布的最近20条收藏信息，和用户收藏页面返回内容是一致的。 
     *  
     * @access public 
     * @param bool $page 返回结果的页序号。 
     * @return array 
     */ 
    function get_favorites( $id_or_screen_name,$count=30,$since_id=false ) 
    { 
        $param = array(); 
        if( $count ) $param['count'] = $count; 
		if( $since_id ) $param['since_id'] = $since_id; 
		 $param['id'] = $id_or_screen_name; 

        return $this->oauth->get( 'http://api.t.163.com/favorites/'.$id_or_screen_name.'.json' , $param ); 
    } 

    /** 
     * 收藏一条微博信息 
     *  
     * @access public 
     * @param mixed $sid 收藏的微博id 
     * @return array 
     */ 
    function add_to_favorites( $sid ) 
    { 

        return $this->oauth->post( 'http://api.t.163.com/favorites/create/'.$sid.'.json' ); 
    } 

    /** 
     * 删除微博收藏。 
     *  
     * @access public 
     * @param mixed $sid 要删除的收藏微博信息ID. 
     * @return array 
     */ 
    function remove_from_favorites( $sid ) 
    { 
        return $this->oauth->post( 'http://api.t.163.com/favorites/destroy/' . $sid . '.json'  ); 
    } 
    
    
    function verify_credentials() 
    { 
        return $this->oauth->get( 'http://api.t.163.com/account/verify_credentials.json' );
    }
    
    function update_avatar( $pic_path )
	{
		$param = array();
		$param['image'] = "@".$pic_path;
        
        return $this->oauth->post( 'http://api.t.163.com/account/update_profile_image.json' , $param , true ); 
	
	} 



    // ========================================= 

    /** 
     * @ignore 
     */ 
    protected function request_with_pager( $url , $page = false , $count = false ) 
    { 
        $param = array(); 
        if( $page ) $param['page'] = $page; 
        if( $count ) $param['count'] = $count; 

        return $this->oauth->get($url , $param ); 
    } 

	protected function request_163( $url , $count = false,$sinace = false,$max=false,$trim=false ) 
    { 
        $param = array(); 
        if( $count ) $param['count'] = $count; 
		 if( $sinace ) $param['$sinace_id'] = $sinace; 
		  if( $max ) $param['max_id'] = $max; 
		   if( $trim ) $param['trim_user'] = $trim; 

        return $this->oauth->get($url , $param ); 
    }

    /** 
     * @ignore 
     */ 
    protected function request_with_uid( $url , $uid_or_name , $page = false , $count = false , $cursor = false , $post = false ) 
    { 
        $param = array(); 
        if( $page ) $param['page'] = $page; 
        if( $count ) $param['count'] = $count; 
        if( $cursor )$param['cursor'] =  $cursor; 

        if( $post ) $method = 'post'; 
        else $method = 'get'; 

        if( is_numeric( $uid_or_name ) ) 
        { 
            $param['user_id'] = $uid_or_name; 
            return $this->oauth->$method($url , $param ); 

        }elseif( $uid_or_name !== null ) 
        { 
            $param['screen_name'] = $uid_or_name; 
            return $this->oauth->$method($url , $param ); 
        } 
        else 
        { 
            return $this->oauth->$method($url , $param ); 
        } 

    } 

} 