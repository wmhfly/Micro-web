<?php
defined('MICRO_PATH') or define('MICRO_PATH',dirname(__FILE__));
/**
 * 框架核心驱动类
 * Micro.lib.Micro
 * ~~~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Micro {
	
	/**
	 * 应用程序实例
	 * @var App
	 */
	private static $_app;
	
	/**
	 * 创建一个应用程序实例
	 * @return App
	 */
	public static function l(){
		return self::$_app = new App();
	}
	
	/**
	 * 获取应用程序
	 * return App
	 */
	public static function app(){
		return self::$_app;
	}
	
	/**
	 * 输出调试日志
	 * @param string $msg
	 */
	public static function trace($msg){
		echo $msg;
	}
	
	#自动装载函数类
	public static function autoload($cls){
		if(is_file($f = MICRO_PATH.DIRECTORY_SEPARATOR.$cls.'.php')!==false){
			include $f;
			return true;
		}
		if(stripos(self::app()->hook->widgetTemp,$cls.'.php')!==false&&is_file($f=self::app()->hook->widgetTemp)!==false){
			include $f;
			return true;
		}
	}
}
#注册自动加载函数
spl_autoload_register(array('Micro','autoload'));