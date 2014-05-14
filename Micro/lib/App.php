<?php
/**
 * 全局应用程序控制类
 * Micro.lib.App
 * ~~~~~~~~~~~~~~~
 * 
 * @property array $settings 应用配置，$_coreMaps的$key核心配置，过滤'filter'=>array('')
 * @property DbConnection $db 数据库连接对象
 * @property HttpRequest $http http连接请求对象
 * @property HttpSession $session session连接请求对象
 * @property FileCache $cache 缓存对象
 * @property Hook $hook widget加载管理器
 * @property Seo $seo 页面优化推广
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class App {
	
	public $settings;
	
	/**
	 * 静态核心类
	 * @var array
	 */
	private static $_coreMaps = array(
			'db'=>'DbConnection',
			'http'=>'HttpRequest',
			'session'=>'HttpSession',
			'cache'=>'FileCache',
			'hook'=>'Hook',
			'seo'=>'Seo'
	);
	
	public function __construct(){
		#载入配置
		$this->settings = include MICRO_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.'settings.php';
		#init 应用内置核心对象
		$this->preInit();
	}
	
	private function preInit(){
		$filters = isset($this->settings['filter'])?$this->settings['filter']:array();
		foreach (self::$_coreMaps as $k=>$cls){
			if(!in_array($k,$filters)){
				$this->$k = isset($this->settings[$k])?new $cls($this->settings[$k]):new $cls();
			}
		}
	}
	
	public function run(){
		$this->hook->initialize();
	}
}