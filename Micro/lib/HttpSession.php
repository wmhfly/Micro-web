<?php
/**
 * http session类
 * Micro.lib.HttpSession
 * ~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class HttpSession {
	
	/**
	 * 构造函数
	 */
	public function __construct(){
		@session_start();
	}
	
	/**
	 * 释放和销毁 session
	 */
	public function destroy()
	{
		if(session_id()!=='')
		{
			@session_unset();
			@session_destroy();
		}
	}
	
	/**
	 * 获取session id
	 */
	public function getSessionID()
	{
		return session_id();
	}
	
	/**
	 * 访问session
	 * @param string $key
	 */
	public function __get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}
	
	/**
	 * 设置session
	 * @param string $key
	 * @param mix $value
	 */
	public function __set($key,$value)
	{
		$_SESSION[$key]=$value;
	}
	
	/**
	 * 消息闪现
	 * @param string $key
	 * @return string|NULL
	 */
	public function getFlash($key){
		if(isset($_SESSION[$key]))
		{
			$value=$_SESSION[$key];
			unset($_SESSION[$key]);
			return $value;
		}
		else
			return null;
	}
	
	public function clear()
	{
		foreach(array_keys($_SESSION) as $key)
			unset($_SESSION[$key]);
	}	
}