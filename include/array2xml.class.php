<?php
/**
@name:Array2xml
@version:1.0
@date:2006-1-22
@license:http://www.gnu.org/copyleft/gpl.html GNU/GPL
@author:axgle <axgle@126.com>
Array2xml is Free Software
*/
class cls_array2xml
{
        var $xml;
        function cls_array2xml($array,$encoding='gb2312')
        {
                $this->xml='<?xml version="1.0" encoding="'.$encoding.'"?>';
                $this->xml.=$this->_array2xml($array);

        }
        function getXml()
        {
                return $this->xml;
        }
        function _array2xml($array,$deep=0)
        {
                $xml='';

				$deepstr=($deep<2)?'':$deep;
				$deep++;

				foreach($array as $key=>$val)
                {
                        is_numeric($key)&&$key="item{$deepstr} id=\"$key\"";
                        $xml.="<$key>";
                        $xml.=is_array($val)?$this->_array2xml($val,$deep):$this->_cdata($val);
                        list($key,)=explode(' ',$key);
                        $xml.="</$key>\n";
                }
                return $xml;
        }
		function _cdata($str){
			$pos = strpos($str, '<');
			if ($pos !== false)$str='<![CDATA['.$str.']]>';
			return $str;

		}
}
?>