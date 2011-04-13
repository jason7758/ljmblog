<?php 

class cls_oauth
{

    public static function &init($target='sina', $consumer_key, $consumer_secret, $oauth_token=NULL, $oauth_token_secret=NULL)      
    { 
        $target = trim($target);
        if(in_array($target, array('sina','qq','163','sohu')))
        {
            $classname = 'cls_oauth'.$target;
            $classfile = str_replace("oauth.class.php","",__FILE__)."oauth".$target.'.class.php';
            include_once($classfile);
            $oauth = new $classname($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL);
            return $oauth;
        }
        exit("不支持该平台（{$target}）的oauth认证");
    }
}