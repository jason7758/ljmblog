//检查同意协议
function agree_check()
{
    var $result = $("#agree").attr("checked");
    if(!$result)
    {
        $.alert("您未同意《新浪网络服务使用协议》,不能创建微群", 1);
    }
    return $result;
}

//处理分类
$(function(){
    if($("#pid").length)    $("#pid").bind_type($("#tid"));
    if($("#aid1").length)   $("#aid1").bind_province($("#aid2"));
});

//绑定检查成功与失败的方法
$.fn.extend({
    //提示检查错误信息
    "check_false" : function($msg)
    {
        $(this).removeClass("cudTs4").addClass("cudTs3");
        $(this).find(".tdCon").html($msg);
    },

    //提示检查正确
    "check_true" : function()
    {
        $(this).removeClass("cudTs3").addClass("cudTs4");
        $(this).find(".tdCon").html("");
    }

});

//创建提交方法
$("#submit").click(function(){
    var $form = $("#creat_form");
    $.ajax({
        "type"     : "post",
        "dataType" : "json",
        "url"      : $form.attr("action"),
        "data"     : $form.serialize(),
        "success"  : function(o){
            if(o.gid)   //成功跳至上传头像页
            {
                $.alert("创建成功！", 3, 1, function(){
                    location.href("/modify.php?gid="+o.gid);
                }, 2000);
            }
            else
            {
                $.alert(o.error, 1, true);
            }
        }
    });
    $("#creat_form").attr("action");
});

//AJAX检查群名称的合法性
$("#nickname").blur(function(){
    $.getJSON("/ajax/group_check.php?action=name", {"name": $(this).val()}, function(o){
        if(o.success)
        {
            $("#nicknameTip").check_true();
        }
        else
        {
            $("#nicknameTip").check_false(o.error);
        }
    });
});

//AJAX检查群介绍的合法性
$("#introduce").blur(function(){
    $.getJSON("/ajax/group_check.php?action=intro", {"intro": $(this).val()}, function(o){
        if(o.success)
        {
            $("#introduceTip").check_true();
        }
        else
        {
            $("#introduceTip").check_false(o.error);
        }
    });
});

//AJAX检查群域名合法性
$("#gdomain").blur(function(){
    $.getJSON("/ajax/group_check.php?action=gdomain", {"gdomain": $(this).val()}, function(o){
        if(o.success)
        {
            $("#gdomainTip").check_true();
        }
        else
        {
            $("#gdomainTip").check_false(o.error);
        }
    });
});
