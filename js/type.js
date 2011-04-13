//取得分类数据
(function(){
    var $type, pid, tid;

    //绑定分类层级JS
    function bind_type(child)
    {
        pid = this;
        tid = child;
        if($type)
        {
            type_callback(o, pid, tid);
        }
        else
        {
            $.getJSON("/ajax/type.php", function(o){
                $type = o;
                type_callback(o, pid, tid);
            });
        }

    }

    //层级分类回调方法
    function type_callback(o, pid, tid)
    {
        var $pid = $(pid), $tid = $(tid);
        $pid.append('<option value="">--请选择--</option>');
        $tid.append('<option value="">--请选择--</option>');
        $.each(o, function(k, v){
            $pid.append('<option value="'+k+'">'+v.sort+'</option>');
        });

        $pid.change(change_callback);

        //设置默认值
        if($pid.attr("val")) $pid.val($pid.attr("val"));
        if($tid.attr("val"))
        {
            change_callback();
            $tid.val($tid.attr("val"));
        }
    }

    //父项改变时触发的事件
    function change_callback()
    {
        var $val = $(pid).val(), $html = "";

        if(!$type[$val] || $type[$val].child_sort.length != 1)
        {
            $html = '<option value="">--请选择--</option>';
        }

        if($type[$val])
        {
            $.each($type[$val].child_sort, function(k, v){
                $html += '<option value="'+v.id+'">'+v.name+'</option>';
            });
        }

        $(tid).html($html);
    }

    jQuery.fn.extend({"bind_type": bind_type});
})(window);