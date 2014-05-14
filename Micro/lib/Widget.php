<?php
/**
 * 组件顶级类
 * Micro.lib.Widget
 * ~~~~~~~~~~~~~~~~~~
 * 
 * @property array $settings 组件配置信息
 * @method run() 静态方法，组件必须实现
 * @method renderPartial() 渲染组件视图
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
abstract class Widget extends Component {
	
	public $settings = array();
	
	public function __construct($config){
		parent::__construct($config);
		$this->settings = $config;
	}
	
	public function __set($key,$val){
		$this->settings[$key] = $val;
	}

	public function __get($key){
		return(isset($this->settings[$key])) ? $this->settings[$key] : null;
	}
	
	/**
	 * 渲染组件视图
	 * @param string $view
	 * @param array $data
	 * @param boolean $return
	 * @return string
	 */
	public function render($data=null,$return=false){
		$viewFile = $this->getViewFile();
		$output = $this->renderInternal($viewFile,$data,true);
		#检测是否写入缓存
		$isCache = M::app()->hook->cache_level==1?$this->cache:false;
		#若模板包含不缓存组件，则不缓存页面
		if(M::app()->hook->cache_level==2&&$this->cache===false)
			M::app()->hook->pageCache = false;
		if($isCache)
			M::app()->cache->set($this->class.'~'.$this->viewFile, $output,M::app()->hook->cache_expire);
		if($return)
			return $output;
		else
			echo $output;
	}
	
	/**
	 * 渲染模版变量
	 * @param string $_viewFile_
	 * @param array $_data_
	 * @param string $_return_
	 */
	public function renderInternal($_viewFile_,$_data_=null,$_return_=false){
		if(is_array($_data_))
			extract($_data_,EXTR_PREFIX_SAME,'data');
		else
			$data=$_data_;
		if($_return_)
		{
			ob_start();
			ob_implicit_flush(false);
			require($_viewFile_);
			return ob_get_clean();
		}
		else
			require($_viewFile_);
	}
	
	/**
	 * 获取视图文件
	 * @return string
	 */
	public function getViewFile(){
		$widgetFolder = dirname(M::app()->hook->widgetTemp);
		return $widgetFolder.DIRECTORY_SEPARATOR.$this->viewFile.'.php';
	}
	
	/**
	 * @return array 数据推送至模板
	 */
	public abstract function run();
}