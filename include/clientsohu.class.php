<?php
/** 
 * 搜狐微博操作类 
 * 
 * @package sae 
 * @author qianyu 
 * @version 1.0 
 */ 
class cls_clientsohu 
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
        $this->oauth = new cls_oauthsohu($akey , $skey , $accecss_token , $accecss_token_secret ); 
    } 

    /** 
     * 最新公共微博 
     *  
     * @access public 
     * @return array 
     */ 
    function public_timeline($count=20) 
    { 
        $resutl = $this->oauth->get('http://api.t.sohu.com/statuses/public_timeline.json'); 
        if(!empty($result))
	 	{
	 	     foreach($result as $key=>$val)
	 	     {
	 	         $result[$key]['thumbnail_pic'] = $val['small_pic'];
                 $result[$key]['bmiddle_pic'] = $val['middle_pic'];
                 unset($result[$key]['small_pic']);
                 unset($result[$key]['middle_pic']);
	 	     }
	 	 }
	 	 return $result;
    } 

    /** 
     * 最新关注人微博 
     *  
     * @access public 
     * @return array 
     */ 
    function friends_timeline($count=20) 
    { 
        return $this->home_timeline($count); 
    } 

    /** 
     * 最新关注人微博 
     *  
     * @access public 
     * @return array 
     */ 
    function home_timeline($count=20) 
    { 
        $result = $this->oauth->get('http://api.t.sohu.com/statuses/home_timeline.json?count='.$count);
        
        if(!empty($result))
	 	{
	 	     foreach($result as $key=>$val)
	 	     {
	 	         $result[$key]['thumbnail_pic'] = $val['small_pic'];
                 $result[$key]['bmiddle_pic'] = $val['middle_pic'];
                 unset($result[$key]['small_pic']);
                 unset($result[$key]['middle_pic']);
	 	     }
	 	 }
	 	return $result;
    } 

    /** 
     * 最新 @用户的 
     *  
     * @access public 
     * @param int $page 返回结果的页序号。 
     * @param int $count 每次返回的最大记录数（即页面大小），不大于200，默认为20。 
     * @return array 
     */ 
    function mentions( $page = 1 , $count = 20 ) 
    { 
        return $this->request_with_pager( 'http://api.t.sohu.com/statuses/mentions.json' , $page , $count ); 
    } 


    /** 
     * 发表微博 
     *  
     * @access public 
     * @param mixed $text 要更新的微博信息。 
     * @return array 
     */ 
    function update( $text ) 
    { 
        //  http://api.t.sohu.com/statuses/update.json 
        $param = array(); 
        $param['status'] = $text; 

        return $this->oauth->post( 'http://api.t.sohu.com/statuses/update.json' , $param );  
    }
    
    /** 
     * 发表图片微博 
     *  
     * @access public 
     * @param string $text 要更新的微博信息。 
     * @param string $text 要发布的图片路径,支持url。[只支持png/jpg/gif三种格式,增加格式请修改get_image_mime方法] 
     * @return array 
     */ 
    function upload( $text , $pic_path ) 
    { 
        //  http://api.t.sohu.com/statuses/update.json 
        $param = array(); 
        $param['status'] = $text; 
        $param['pic'] = (strpos("http://",$pic_path) ? '@' : '').$pic_path;
        
        return $this->oauth->post( 'http://api.t.sohu.com/statuses/upload.json' , $param , true ); 
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
        return $this->oauth->get( 'http://api.t.sohu.com/statuses/show/' . $sid . '.json' ); 
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
        return $this->oauth->post( 'http://api.t.sohu.com/statuses/destroy/' . $sid . '.json' ); 
    } 

    /** 
     * 个人资料 
     *  
     * @access public 
     * @param mixed $uid_or_name 用户UID或微博昵称。 
     * @return array 
     */ 
    function show_user( $uid_or_name = null ) 
    { 
        return $this->request_with_uid( 'http://api.t.sohu.com/users/show.json' ,  $uid_or_name ); 
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
    function friends( $cursor = false , $count = false , $uid_or_name = null ) 
    { 
        return $this->request_with_uid( 'http://api.t.sohu.com/statuses/friends.json' ,  $uid_or_name , false , $count , $cursor ); 
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
    function followers( $cursor = false , $count = false , $uid_or_name = null ) 
    { 
        return $this->request_with_uid( 'http://api.t.sohu.com/statuses/followers.json' ,  $uid_or_name , false , $count , $cursor ); 
    } 

    /** 
     * 关注一个用户 
     *  
     * @access public 
     * @param mixed $uid_or_name 要关注的用户UID或微博昵称 
     * @return array 
     */ 
    function follow( $uid_or_name ) 
    { 
        return $this->request_with_uid( 'http://api.t.sohu.com/friendships/create.json' ,  $uid_or_name ,  false , false , false , true  ); 
    } 

    /** 
     * 取消关注某用户 
     *  
     * @access public 
     * @param mixed $uid_or_name 要取消关注的用户UID或微博昵称 
     * @return array 
     */ 
    function unfollow( $uid_or_name ) 
    { 
        return $this->request_with_uid( 'http://api.t.sohu.com/friendships/destroy.json' ,  $uid_or_name ,  false , false , false , true); 
    } 

    /** 
     * 返回两个用户关系的详细情况 
     *  
     * @access public 
     * @param mixed $uid_or_name 要判断的用户UID 
     * @return array 
     */ 
    function is_followed( $uid_or_name ) 
    { 
        $param = array(); 
        if( is_numeric( $uid_or_name ) ) $param['target_id'] = $uid_or_name; 
        else $param['target_screen_name'] = $uid_or_name; 

        return $this->oauth->get( 'http://api.t.sohu.com/friendships/show.json' , $param ); 
    } 

    /** 
     * 用户发表微博列表 
     *  
     * @access public 
     * @param int $page 页码 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认20。 
     * @param mixed $uid_or_name 指定用户UID或微博昵称 
     * @return array 
     */ 
    function user_timeline( $page = 1 , $count = 20 , $uid_or_name = null ) 
    { 
        if( !is_numeric( $page ) ) 
            return $this->request_with_uid( 'http://api.t.sohu.com/statuses/user_timeline.json' ,  $page ); 
        else 
            return $this->request_with_uid( 'http://api.t.sohu.com/statuses/user_timeline.json' ,  $uid_or_name , $page , $count ); 
    } 

    /** 
     * 获取私信列表 
     *  
     * @access public 
     * @param int $page 页码 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认20。 
     * @return array 
     */ 
    function list_dm( $page = 1 , $count = 20  ) 
    { 
        return $this->request_with_pager( 'http://api.t.sohu.com/direct_messages.json' , $page , $count ); 
    } 

    /** 
     * 发送的私信列表 
     *  
     * @access public 
     * @param int $page 页码 
     * @param int $count 每次返回的最大记录数，最多返回200条，默认20。 
     * @return array 
     */ 
    function list_dm_sent( $page = 1 , $count = 20 ) 
    { 
        return $this->request_with_pager( 'http://api.t.sohu.com/direct_messages/sent.json' , $page , $count ); 
    } 

    /** 
     * 发送私信 
     *  
     * @access public 
     * @param mixed $uid_or_name UID或微博昵称 
     * @param mixed $text 要发生的消息内容，文本大小必须小于300个汉字。 
     * @return array 
     */ 
    function send_dm( $uid_or_name , $text ) 
    { 
        $param = array(); 
        $param['text'] = $text; 

        if( is_numeric( $uid_or_name ) ) $param['user_id'] = $uid_or_name; 
        else $param['screen_name'] = $uid_or_name; 

        return $this->oauth->post( 'http://api.t.sohu.com/direct_messages/new.json' , $param  ); 
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
        return $this->oauth->post( 'http://api.t.sohu.com/direct_messages/destroy/' . $did . '.json' ); 
    } 

    /** 
     * 转发一条微博信息。 
     *  
     * @access public 
     * @param mixed $sid 转发的微博ID 
     * @param bool $text 添加的转发信息。 
     * @return array 
     */ 
    function repost( $sid , $text = false ) 
    { 
        $param = array(); 
        $param['id'] = $sid; 
        if( $text ) $param['status'] = $text; 

        return $this->oauth->post( 'http://api.t.sohu.com/statuses/repost.json' , $param  ); 
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

        return $this->oauth->post( 'http://api.t.sohu.com/statuses/comment.json' , $param  ); 

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
        return $this->request_with_pager( 'http://api.t.sohu.com/statuses/comments_by_me.json' , $page , $count ); 
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
        return $this->request_with_pager( 'http://api.t.sohu.com/statuses/comments_timeline.json' , $page , $count ); 
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
    function get_comments_by_sid( $sid , $page = 1 , $count = 20 ) 
    { 
        $param = array(); 
        $param['id'] = $sid; 
        if( $page ) $param['page'] = $page; 
        if( $count ) $param['count'] = $count; 

        return $this->oauth->get('http://api.t.sohu.com/statuses/comments.json' , $param ); 

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

        return $this->oauth->get( 'http://api.t.sohu.com/statuses/counts.json' , $param ); 
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

        return $this->oauth->post( 'http://api.t.sohu.com/statuses/reply.json' , $param  ); 

    } 

    /** 
     * 返回用户的发布的最近20条收藏信息，和用户收藏页面返回内容是一致的。 
     *  
     * @access public 
     * @param bool $page 返回结果的页序号。 
     * @return array 
     */ 
    function get_favorites( $page = false ) 
    { 
        $param = array(); 
        if( $page ) $param['page'] = $page; 

        return $this->oauth->get( 'http://api.t.sohu.com/favorites.json' , $param ); 
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
        $param = array(); 
        $param['id'] = $sid; 

        return $this->oauth->post( 'http://api.t.sohu.com/favorites/create.json' , $param ); 
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
        return $this->oauth->post( 'http://api.t.sohu.com/favorites/destroy/' . $sid . '.json'  ); 
    } 
    
    
    function verify_credentials() 
    { 
        return $this->oauth->get( 'http://api.t.sohu.com/account/verify_credentials.json' );
    }
    
    function update_avatar( $pic_path )
	{
		$param = array();
		$param['image'] = "@".$pic_path;
        
        return $this->oauth->post( 'http://api.t.sohu.com/account/update_profile_image.json' , $param , true ); 
	
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
