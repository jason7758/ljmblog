<?php 
include_once "Smarty/Smarty.class.php";
class cls_smarty extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        $this->template_dir = TPL_DIR;
        $compile_dir = $_SERVER['SINASRV_CACHE_DIR']."/mblog_templates_c";		
        if (!is_dir($compile_dir))
        {
                mkdir($compile_dir, 0777);
        }
        $this->compile_dir  = $compile_dir;

        $this->left_delimiter = "<!--{";
        $this->right_delimiter = "}-->";
        $this->compile_check  = true;
        $this->force_compile = true;
        //$this->cache_dir    = $compile_dir;
        $this->caching = false;
        $this->debugging = false;
    }
	public function set_refresh($url = '/', $time = 3)
	{
		$meta_refresh = '<meta http-equiv="refresh" content="'.$time.'; url='.$url.'">';       
		$this->assign('meta_refresh', $meta_refresh);
	}
	
	public function assign($tpl_var, $value = null)
	{
		parent::assign($tpl_var, $value);
		return $this;
	}
	
	//显示页面
	public function display($resource_name, $cache_id=null, $compile_id=null)
	{		
		parent::display($resource_name, $cache_id, $compile_id);
	}
}
?>
