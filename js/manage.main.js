jQuery.extend({
    //AJAX简化提交方法
    "ajax_submit" : function(obj, $goto, $msg)
    {        
        var $obj = $(obj), $submit = $obj.find(":submit"), $action=$obj.attr("action");
        $submit.after("<img src='/images/manage/loading/small.gif' alt='Loading...' />").hide();

        $.ajax({
            "url"   : $action ? $action : location.href,
            "type"  : $obj.attr("method"),
            "data"  : $obj.serialize(),
            "dataType" : "json",
            "success"  : function(o){
                if($.isFunction($goto))
                {
                    $submit.show().next("img").remove();
                    $goto(o);
                    return false;
                }

                if(o.success)
                {
                    alert($msg ? $msg : "恭喜您！操作成功！");
                    location.href = $goto ? $goto : location.href;
                }
                else
                {
                    $submit.show().next("img").remove();
                    alert(o.message);
                }
                return true;
            }
        });
        
        return false;
    }
});

$(function(){
    var $out_border_color = "#525C3D";

    //纵向双色表格Hover
    $("table.result > tbody td").hover(function(){
        $(this).addClass("hover").siblings().addClass("hover");
    }, function(){
        $(this).removeClass("hover").siblings().removeClass("hover");
    })

    //横向双色表格及Hover
    $("table.form tr").hover(function(){
        $(this).addClass("hover");
    }, function(){
        $(this).removeClass("hover");
    }).filter(":odd").addClass("odd");

    //绘制表格背景和表格线
    $("table.result tr, table.form tr").each(function(){
        $(this).children("td:odd").addClass("odd");
        $(this).children("td:first, th:first").css("border-left-color", $out_border_color);
        $(this).children("td:last, th:last").css("border-right-color", $out_border_color);
    })
    $("table.result tr:last td").css("border-bottom-color", $out_border_color);

    //设置表单表格属性
    $("table.form").attr("cellspacing", "0");

    //修正Webkit下caption宽度错误的问题
    if($.browser.safari)
    {
        $("table caption").each(function(){

            var $obj = $(this);
            $obj.width($obj.parent().width());

            $(window).one("resize", function(){
                $obj.width("auto");
            })
        })
    }
})
