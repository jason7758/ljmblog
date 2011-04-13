//js常用函数--
jQuery.extend({

//加入收藏夹
add_to_favorite: function()
{
    if (document.all)
    {
        window.external.addFavorite(document.location.href,document.title);
    }
    else if (window.sidebar)
    {
        window.sidebar.addPanel(document.title,document.location.href, "");
    }
},

//载入HTML并加载至页面底部
load_html_bottom: function($url, id, $async)
{
    if(!$("#"+id).length)
    {
        $async = $async ? true : false;
        $.ajax({url:$url, async:false, type:"get", cache:true, dataType:"html", success:function(h){
            $("body").append(h);
        }});
    }
},

//载入CSS
load_css: function($url, $call_back)
{
    $.get($url, function(h){
        $("head").append('<style type="text/css">'+h+'</style>');
        if($.isFunction($call_back))    $call_back();
    })
},

//将指定参数强制转换为INT
intval: function($n)
{
    $n = parseInt($n);
    if(isNaN($n)) $n = 0;
    return $n;
},

//通过微博OPEN接口取得数据
mblog_get : function($url, $param, $callback)
{
    var $base_url = "http://api.t.sina.com.cn/";    //备用URL：/ajax/open_api.php
    $url = $base_url+$url+"?callback=?";
    $data = {source:2163441927};
    

    if($.isFunction($param) && !$.isFunction($callback))
    {
        $param = $data;
        $callback = $param;
    }
    else
    {
        $param = $.extend($data, $param);
    }

    $.getJSON($url, $param, $callback);
}

});


//提示按钮
(function(){
    //初始化操作
    function init_msg()
    {
        $("#confirm_button,#close_cross").show();
    }

	//隐藏某一元素(同时去掉遮罩)
	function hide_element(idString)
	{
		$("#"+idString).hide();
		if($("#floatBoxBg").length > 0)
		{
			$.cancel_mask();
		}
	}

    jQuery.extend({
        //添加遮罩
        add_mask: function()
        {
            if(!$("#floatBoxBg").length)
            {
                var $temp_float;
                $temp_float=$("<div id=\"floatBoxBg\" style=\"display:block;height:"+$(document).height()+"px;filter:alpha(opacity=0.5);opacity:0.5;\"></div>");
                $("body").append($temp_float);
            }
            else
            {
                $("#floatBoxBg").show().css({opacity:"0.5"});
            }
            
        },

        //取消遮罩
        cancel_mask: function()
        {
            $("#floatBoxBg").css({opacity:"0"}).hide();
        },

        //显示提示信息
        alert: function($msg, $type, $mask, $callback, $time_out)
        {
            $.load_html_bottom("/ajax/html/alert.html", "my_confirm");
            init_msg();

            $type = $type || 1;
            $("#msg_img_confirm").attr("class","PY_ib_"+$type);
            $("#text_confirm").html($msg);
            $("#my_confirm #cancel_confirm").hide();

            if($time_out>0)
            {
                $("#my_confirm #do_confirm").hide();
                setTimeout(function(){
                    hide_element('my_confirm');
                    if($.isFunction($callback)) $callback();
                    return false;
                }, $time_out*1000);
            }
            else
            {
                $("#do_confirm").show();
                $("#do_confirm,#close_cross").unbind("click").click(function()
                {
                    hide_element('my_confirm');
                    if($.isFunction($callback)) $callback();
                    return false;
                });
            }
            $("#my_confirm").css('z-index', '99999').show();
            if(typeof($mask)=="undefined" || $mask) $.add_mask();
        },

        //弹出询问框 type:1叹2叉3勾4问
        confirm: function($msg, $do_cb, $cancle_cb, $mask, $type)
        {
            $.load_html_bottom("/ajax/html/alert.html", "my_confirm");
            if(!$type) $type=4;
            $("#do_confirm, #cancel_confirm").css("display", "");
            $("#msg_img_confirm").attr("class","PY_ib_"+$type);
            $("#text_confirm").html($msg);
            $("#my_confirm").show();

            
            $("#do_confirm").unbind("click").click(function(){
                hide_element('my_confirm');
                if($.isFunction($do_cb))    $do_cb();
            })

            $("#cancel_confirm, #close_cross").unbind("click").click(function(){
                hide_element('my_confirm');
                if($.isFunction($cancle_cb))    $cancle_cb();
            });
            if(typeof($mask)=="undefined" || $mask) $.add_mask();
        }
    });
})()



//jQuery.fn扩展
jQuery.fn.extend({

//给输入框绑定默认的文件提示
default_value : function($val)
{
    var $obj = $(this);
    if(!$obj.val())
    {
        $obj.val($val).css("color", "#878787");
    }

    $obj.click(function(){
        if($obj.val() == $val)	$obj.val("").css("color", "");
    });

    $obj.blur(function(){
        if(!$obj.val()) $obj.val($val).css("color", "#878787");
    });

    return function()
    {
        if($obj.val() == $val)	$obj.val("").css("color", "");
    }
},

//全选
select_all: function(name)
{
    var $checked = this.attr("checked");
    $(":checkbox[name='"+name+"']").attr("checked", $checked);
}



});