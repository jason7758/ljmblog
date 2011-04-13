(function(){
    var $time=1500, $member_loaded=false, $t_my, $t_group;

    //我的首页
    $("#menu_my").hover(function(){
        $("#menu_group > div").hide();
        clearTimeout($t_my);        
        $(this).children("div").show();
    }, function(){
        $t_my = setTimeout(function(){
            $("#menu_my > div").hide();
        }, $time);
    });

    //我的微群
    $("#menu_group").hover(function(){
        $("#menu_my > div").hide();
        clearTimeout($t_group);
        if(!$member_loaded)
        {
            $member_loaded = true;
            $.getJSON("/ajax/member.php?action=joined&limit=4", function(o){
                var $html="";
                if(o.total)
                {
                    $.each(o.result, function(k, v){
                        $html += '<a target="_blank" href="'+v.url+'">'+v.grp_name+'</a>';
                    });

                    if(o.total == 4)
                    {
                        $html += '<a target="_blank" href="/s/list">'+更多+'</a>';
                    }
                    
                }
                else
                {
                    $html = '<a target="_blank" href="/s/slist" style="color:#000;">您还未加入任何微群，请查找您感兴趣的微群，加入吧！</a>';
                }
                $("#menu_group > div > p:first").html($html);
            });
        }

        $(this).children("div").show();       
    }, function(){
        $t_group = setTimeout(function(){
            $("#menu_group > div").hide();
        }, $time);
    });
})(window);

//图片延迟加载
$("img").lazyload({placeholder:"/images/grey.gif",threshold:50,defined_src:"lsrc"});

