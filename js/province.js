//取得地区数据
(function(){
    var $data, province, city;

    //绑定分类层级JS
    function bind_province(child)
    {
        province = this;
        city = child;
        if($data)
        {
            type_callback(o, province, city);
        }
        else
        {
            $.getJSON("/ajax/province.php", function(o){
                $data = o;
                type_callback(o, province, city);
            });
        }
    }

    //层级分类回调方法
    function type_callback(o, province, city)
    {
        var $province = $(province), $city = $(city);
        $province.append('<option value="">--请选择--</option>');
        $city.append('<option value="">--请选择--</option>');

        $.each(o, function(k, v){
            $province.append('<option value="'+k+'">'+v.name+'</option>');
        });

        $province.change(change_callback);

        //设置默认值
        if($province.attr("val"))   $province.val($province.attr("val"));
        if($city.attr("val"))
        {
            change_callback();
            $city.val($city.attr("val"));
        }
    }

    //父项改变时触发的事件
    function change_callback(){
        var $val = $(province).val(), $html = "", $i=0;

        if($data[$val])
        {
            $.each($data[$val].city, function(k, v){
                $i++;
                $html += '<option value="'+k+'">'+v+'</option>';
            });
        }

        if($i > 1 || !$data[$val])
        {
            $html = '<option value="">--请选择--</option>' + $html;
        }

        $(city).html($html);
    }

    jQuery.fn.extend({"bind_province": bind_province});
})(window);