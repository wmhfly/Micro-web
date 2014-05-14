<?php
/**
 * 文件缓存类(默认序列化存儲)
 * Micro.lib.FileCache
 * ~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class FileCache extends Component {
	/**
	 * @var string 缓存路径
	 */
	public $cachePath;
	/**
	 * @var string 缓存文件后缀
	 */
	public $cacheFileSuffix='';
	/**
	 * @var string 缓存清单文件
	 */
	public $cacheMenuName='_CACHE_MENU_.php';
	/**
	 * @var array 缓存清单
	 */
	private $_cacheMenu;
	private $_gcProbability=100;
	private $_gced=false;
	
	public function __construct($config=''){
		parent::__construct($config);
		
		$this->preInit();
	}
	
	private function preInit(){
		if($this->cachePath===null)
			$this->cachePath=MICRO_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cache';
		if(!is_dir($this->cachePath))
			mkdir($this->cachePath,0777,true);
		if(is_file($f=$this->getCacheMenuPath())===false){
			@file_put_contents($f, "<?php\r\nreturn array();");
			@chmod($f,0777);
		}
	}
	
	/**
	 * 
	 * @param string $key
	 * @param mix $value
	 * @param int $expire 默认1 year
	 */
	public function set($key,$value,$expire=0){
		//自动回收过期缓存，有万分之一的可能会执行-删除过期缓存文件操作
		if(!$this->_gced && mt_rand(0,1000000)<$this->_gcProbability)
		{
			$this->gc();
			$this->_gced=true;
		}
		
		if($expire<=0)
			$expire=31536000; // 1 year
		$_time = time();
		$expire+=$_time;
		
		$cacheFile=$this->getCacheFile($key);
		if(@file_put_contents($cacheFile,$expire.serialize($value),LOCK_EX)!==false)
		{
			$this->initCacheMenu();
			$this->_cacheMenu[$key] = @filesize($cacheFile).'|'.$_time.'|'.$expire;
			@chmod($cacheFile,0777);
			return true;
		}
		else
			return false;
		
	}
	/**
	 * 过期缓存回收
	 * @param string $expiredOnly
	 * @param string $path
	 */
	public function gc($expiredOnly=true,$path=null)
	{
		if($path===null)
			$path=$this->cachePath;
		if(($handle=opendir($path))===false)
			return;
		while(($file=readdir($handle))!==false)
		{
			if($file[0]==='.')
				continue;
			$fullPath=$path.DIRECTORY_SEPARATOR.$file;
			if(is_dir($fullPath))
				$this->gc($expiredOnly,$fullPath);
			elseif($expiredOnly && $this->filemtime($fullPath)<time() || !$expiredOnly)
				$this->updateAndDelete($file, $fullPath);
		}
		closedir($handle);
	}
	
	/**
	 * 获取缓存
	 * @param string $key
	 * @return data|false
	 */
	public function get($key){
		$cacheFile=$this->getCacheFile($key);
		if(($time=$this->filemtime($cacheFile))>time()){
			$value = @file_get_contents($cacheFile,false,null,10);
			return unserialize($value);
		}elseif($time>0)
			$this->updateAndDelete($key, $cacheFile);
			return false;
	}
	
	/**
	 * 删除缓存
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key){
		$cacheFile=$this->getCacheFile($key);
		$this->updateAndDelete($key, $cacheFile);
		return true;
	}
	
	/**
	 * 强制清空缓存
	 * @return boolean
	 */
	public function flush(){
		$this->gc(false);
		return true;
	}
	
	/**
	 * 获取缓存过期时间
	 * @param string $path
	 * @return number
	 */
	private function filemtime($path){
		return (int)@file_get_contents($path,false,null,0,10);
	}
	
	/**
	 * 更新缓存清单和删除缓存文件
	 * @param string $key
	 * @param string $path
	 */
	private function updateAndDelete($key,$path){
		$this->initCacheMenu();
		unset($this->_cacheMenu[$key]);
		@unlink($path);
	}
	
	/**
	 * 初始化缓存清单列表
	 * @return {@link cacheMenu}
	 */
	private function initCacheMenu(){
		if($this->_cacheMenu===null)
			$this->_cacheMenu = include($this->getCacheMenuPath());
	}
	
	/**
	 * 获取缓存文件路径
	 * @param string $key
	 * @return string
	 */
	public function getCacheFile($key){
		return $this->cachePath.DIRECTORY_SEPARATOR.md5($key);
	}
	
	/**
	 * 获取缓存菜单路径
	 * @return string
	 */
	public function getCacheMenuPath(){
		return $this->cachePath.DIRECTORY_SEPARATOR.$this->cacheMenuName;
	}
	
	/**
	 * GET 获取缓存清单列表
	 * @return array
	 */
	public function getCacheMenu(){
		$this->initCacheMenu();
		return $this->_cacheMenu;
	}
	
	/**
	 * 析构函数，最后保存修改缓存清单
	 */
	public function __destruct() {
		$cacheMenuFile = $this->getCacheMenuPath();
		if(is_file($cacheMenuFile)&&$this->_cacheMenu!==null){
			$cacheMenuStr = var_export($this->_cacheMenu,true);
			$cacheMenuStr = "<?php\r\nreturn ".$cacheMenuStr.";";
			@file_put_contents($cacheMenuFile, $cacheMenuStr);
			@chmod($cacheMenuFile,0777);
		}
	}
	
}

?>