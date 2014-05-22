<?php
/**
 * 视图和组件加载器
 * Micro.lib.Hook
 * ~~~~~~~~~~~~~~~~
 * @property string $viewFolder 视图路径，默认MICRO_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR 
 * @property string $widgetFolder 组件路径，默认MICRO_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR
 * @property string $widgetTemp 组件类路径
 * @property string $pageCache 页面缓存，如果模板某一组件不缓存则页面不缓存
 * @property int $cache_level 缓存级别(1为组件缓存；2为页面缓存；默认为0，不缓存)
 * @property int $cache_expire 缓存时间，默认30天
 * @property string $layoutFile layout 布局视图路径
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */

class Hook extends Component {
	
	public $viewFolder;
	public $widgetTemp;
	public $widgetFolder;
	public $pageCache = true;
	public $cache_level = 0;
	public $cache_expire = 2592000;
	public $layoutFile = 'Main';
	public $defaultTemplate = 'default';	
	
	
	public function __construct($config=''){
		parent::__construct($config);
		
		$this->preInit();
	}
	
	/**
	 * init view & widget folder
	 */
	private function preInit(){
		if(!$this->viewFolder)
			$this->viewFolder = MICRO_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
		if(!$this->widgetFolder)
			$this->widgetFolder = MICRO_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR;
	}
	
	/**
	 * 挂载渲染模板
	 * 根据http的 AJAX,POST,GET做请求分发
	 */
	public function initialize(){
		$runRequest = 'handler'.M::app()->http->getRequestType();
		$this->$runRequest();
	}
	
	private function handlerAJAX(){
		$wgt = M::app()->http->v? M::app()->http->v:'async';
		$act = M::app()->http->act? M::app()->http->act:'run';
		$this->widget($wgt,array(),$act);
	}
	
	private function handlerPOST(){
		$wgt = M::app()->http->v? M::app()->http->v:'handler';
		$act = M::app()->http->act? M::app()->http->act:'handler';
		$this->widget($wgt,array(),$act);
	}
	
	private function handlerGET(){
		$tpl = $this->getViewTemplate(M::app()->http->v);
		#检测是否存在缓存
		$cKey = M::app()->http->getCacheUri();
		if($this->pageCache&&$this->cache_level==2)
			$output = M::app()->cache->get($cKey);
		#是否重新渲染
		if(!$output){
			$viewFile = $this->getViewFile($tpl);
			$output = $this->renderInternal($viewFile,null,true);
			if(stripos($viewFile, DIRECTORY_SEPARATOR.'_')===false && is_file(($layoutFile=$this->getLayoutFile($this->layoutFile)))!==false)
				$output=$this->renderInternal($layoutFile,array('content'=>$output),true);
			
			#检测是否写入缓存
			if($this->pageCache&&$this->cache_level==2)
				M::app()->cache->set($cKey, $output, $this->cache_expire);
		}	
		echo $output;
	}
	
	
	
	/**
	 * 渲染widget
	 * @param string $cls
	 * @param array $settings
	 * @param boolean $run
	 */
	public function widget($widget,$settings = array(),$run = null){
		$settings = $this->getWidgetSetting($widget,$settings);	
		#是否启用组件
		if(isset($settings['lock']))
			return;
		#检测是否存在缓存
		$isCache = $this->cache_level==1?(isset($settings['cache'])?$settings['cache']:false):false;
		$cls = $settings['class'];
		if(is_null($run)&&$isCache&&($output=M::app()->cache->get($cls.'~'.$settings['viewFile']))!==false){
			echo $output;
		}else{
			$this->widgetTemp = $this->getWidgetFile($widget,$cls);
			$wgt = new $cls($settings);
			#如果是 AJAX || POST 请求
			if($run){
				$wgt->$run();
			}else
				$wgt->render($wgt->run());
		}
	}
	
	/**
	 * 组件配置数据
	 * @param string $widget
	 * @param array $settings
	 * @return array
	 */
	public function getWidgetSetting($widget,$settings){
		$data = array();
		#检测组件是否启用
		if(is_file($sFile = $this->widgetFolder.$widget.DIRECTORY_SEPARATOR.'lock')!==false)
			return array('lock'=>true);
		if(is_file($sFile = $this->widgetFolder.$widget.DIRECTORY_SEPARATOR.'settings.php')!==false)
			$data = include $sFile;
		
		#覆盖和扩展定义
		foreach ($settings as $k=>$v)
			$data[$k] = $v;
		if(!isset($data['class'])) $data['class'] = $widget;
		if(!isset($data['viewFile'])) $data['viewFile'] = '_view';
		return $data;
	}
	
	/**
	 * 组件文件
	 * @param string $widget
	 * @return string|boolean
	 */
	public function getWidgetFile($widget,$_widget){
		if(is_file($view = $this->widgetFolder.$widget.DIRECTORY_SEPARATOR.$_widget.'.php')!==false)
			return $view;
		$_view = sprintf('組件類不存在， %s',$view);
		M::trace($_view);
		return false;
	}
	
	/**
	 * 视图文件
	 * @param string $view
	 */
	public function getViewFile($view){
		$view = $this->getViewTemplate($view);
		foreach (array('','_') as $item){
			if(is_file($_view=$this->viewFolder.$item.$view.'.php')!==false)
				return $_view;			
		}
		$_view = sprintf('视图不存在， %s',$view);
		M::trace($_view);
		return false;
	}
	
	/**
	 * 视图template name
	 * @param string $view
	 * @return string
	 */
	private function getViewTemplate($view){
		return $view?$view:$this->defaultTemplate;
	}
	
	/**
	 * layout 文件
	 * @param string $layout
	 */
	public function getLayoutFile($layout){
		return $this->viewFolder.'layouts'.DIRECTORY_SEPARATOR.$layout.'.php';
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
	
}