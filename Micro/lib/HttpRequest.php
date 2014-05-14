<?php
/**
 * http 请求类
 * Micro.lib.HttpRequest
 * ~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class HttpRequest {
	
	private $_requestUri;
	private $_scriptUrl;
	
	/**
	 * 构造函数
	 */
	public function __construct(){
		$this->normalizeRequest();
	}
		
	/**
	 * HTTP Request 反转义
	 */
	private function normalizeRequest(){
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			if(isset($_GET))
				$_GET=$this->stripSlashes($_GET);
			if(isset($_POST))
				$_POST=$this->stripSlashes($_POST);
			if(isset($_REQUEST))
				$_REQUEST=$this->stripSlashes($_REQUEST);
			if(isset($_COOKIE))
				$_COOKIE=$this->stripSlashes($_COOKIE);
		}
	}
	/**
	 * 转义字符
	 * @param unknown_type $data
	 */
	private function stripSlashes(&$data)
	{
		return is_array($data)?array_map(array($this,'stripSlashes'),$data):stripslashes($data);
	}
	
	/**
	 * http GET 参数 & 路由规则参数
	 * @param string $k
	 * @return string
	 */
	public function __get($k){
		return isset($_GET[$k]) ? $_GET[$k] : '';
	}
	
	/**
	 * 获取GET数据
	 * @param string $name
	 * @param mix $defaultValue
	 */
	public function getQuery($name,$defaultValue=null)
	{
		return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}
	
	/**
	 * 获取POST数据
	 * @param string $name
	 * @param mix $defaultValue
	 */
	public function getPost($name,$defaultValue=null)
	{
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}
	
	/**
	 * 一次性获取请求data
	 * @param array|string $data
	 * @param string $type
	 */
	public function getReqData($data,$type='G'){
		if(is_array($data)){
			foreach($data as $key){
				$tempData[$key] = ($type=='G') ? (isset($_GET[$key])? $_GET[$key] : ''): (isset($_POST[$key])? $_POST[$key] : '');
			}
		}else {
			$tempData = ($type=='G') ? (isset($_GET[$data])? $_GET[$data] : ''): (isset($_POST[$data])? $_POST[$data] : '');
		}
		return $tempData;
	}
	
	/**
	 * 重定向
	 * @param string $url
	 * @param number $statusCode
	 */
	public function redirect($url,$statusCode=302)
	{
		header('Location: '.$url, true, $statusCode);
		exit;
	}
	
	/**
	 * 获取脚本路径
	 */
	public function getScriptUrl()
	{
		if($this->_scriptUrl===null)
		{
			$scriptName=basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
				$this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
			else if(basename($_SERVER['PHP_SELF'])===$scriptName)
				$this->_scriptUrl=$_SERVER['PHP_SELF'];
			else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
				$this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
			else if(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
				$this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
				$this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
		}
		return $this->_scriptUrl;
	}

	/**
	 * 获取当前请求url
	 */
	public function getRequestUri()
	{
		if($this->_requestUri===null)
		{
			if(isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
				$this->_requestUri=$_SERVER['HTTP_X_REWRITE_URL'];
			else if(isset($_SERVER['REQUEST_URI']))
			{
				$this->_requestUri=$_SERVER['REQUEST_URI'];
				if(!empty($_SERVER['HTTP_HOST']))
				{
					if(strpos($this->_requestUri,$_SERVER['HTTP_HOST'])!==false)
						$this->_requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$this->_requestUri);
				}
				else
					$this->_requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$this->_requestUri);
			}
			else if(isset($_SERVER['ORIG_PATH_INFO']))  // IIS 5.0 CGI
			{
				$this->_requestUri=$_SERVER['ORIG_PATH_INFO'];
				if(!empty($_SERVER['QUERY_STRING']))
					$this->_requestUri.='?'.$_SERVER['QUERY_STRING'];
			}
		}
	
		return $this->_requestUri;
	}
	
	/**
	 * 返回用户ip 地址
	 * @return string user IP address
	 */
	public function getUserHostAddress()
	{
		return isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
	}
	
	/**
	 * 是否POST 請求
	 */
	public function isPostRequest()
	{
		return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST');
	}
	
	/**
	 * Returns whether this is an AJAX (XMLHttpRequest) request.
	 * @return boolean whether this is an AJAX (XMLHttpRequest) request.
	 */
	public function isAjaxRequest()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}
	
	/**
	 * 请求类型
	 * @return string AJAX POST GET
	 */
	public function getRequestType()
	{	
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest') ? 'AJAX':strtoupper(isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'GET');
	}
	
	/**
	 * @return string 页面缓存url对应缓存key
	 */
	public function getCacheUri(){
		return isset($_SERVER['QUERY_STRING'])&&$_SERVER['QUERY_STRING']?$_SERVER['QUERY_STRING']:'v=default';
	}
}