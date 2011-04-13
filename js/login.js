var sinaSSOConfig, sso_callback;

$.fn.extend({
    //SSO登录事件绑定
    'sso_login' : function(uname, upass, savestate, callback)
    {
        if(!$.isFunction(callback))
        {
            sso_callback = function(o)
            {
                if(o.result)
                {
                    location.href = '';
                }
                else
                {
                    alert(o.reason);
                }
            }
        }

        sinaSSOConfig = new function() {
            this.isCheckLoginState = false;
            this.customLoginCallBack = sso_callback;
        };

        
        var $uname=$(uname), $upass=$(upass), $savestate=$(savestate);

        //处理自动下拉提示
        _usernameObj = $uname[0];
        if(!_usernameObj)   return false;

        function initUserName()
        {
            passcardOBJ.init(
                // FlashSoft 注意,最好这个input的autocomplete设定为off
                // 需要有下拉框的input对象
                _usernameObj,
                {
                    // 鼠标经过字体颜色
                    overfcolor: "#666",
                    // 鼠标经过背景颜色
                    overbgcolor: "#d6edfb",
                    // 鼠标离开字体颜色
                    outfcolor: "#000000",
                    // 鼠标离开背景颜色
                    outbgcolor: ""
                },
                // 输入完成后,自动需要跳到的input对象[备选]
                $upass[0],
                null
            );
        }



        $uname.attr("autocomplete", "off");
        if(typeof(passcardOBJ) == "undefined")
        {
            $("head").append('<link href="http://i.sso.sina.com.cn/css/cardtips.css" rel="stylesheet" type="text/css" media="all" />');
            $.getScript("/js/cardtips.js", initUserName);
        }
        else
        {
            initUserName();
        }

        //定义登录方法
        function _login()
        {
            var name = encodeURIComponent($uname.val());
            var pass = encodeURIComponent($upass.val());
            var savestate = $savestate.attr("checked") ? "&savestate=7" : "";
            var url = 'http://i.house.sina.com.cn/sso/login.php?name='+name+'&pass='+pass+'&returntype=TEXT'+savestate;
            $.getScript(url);
        }

        //绑定提交事件
        $(this).submit(function(){
            if(typeof(lejuSSOController) == 'undefined')
            {
                $.getScript("http://i.house.sina.com.cn/js/sso.js", _login);
            }
            else
            {
                _login();
            }
           
            return false;
        })
    }
});