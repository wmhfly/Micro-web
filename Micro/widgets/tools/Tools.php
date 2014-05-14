<?php
/**
 * Tools
 * Micro.widgets.tools.Tools
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Tools extends Widget {
	
	public function run(){
		switch ($this->viewFile){
			case 'WTool':
				$result = array();
			break;
			case 'CTool':
				if(M::app()->http->f)
					$this->clearCache(M::app()->http->f);
				
				$result['list'] = $this->getCaches();
			break;
			case 'MTool':
				$result['list'] = $this->getWidgets();
			break;
		}
		return $result;
	}
	
	/**
	 * 获取组件清单
	 * @return array
	 */
	private function getWidgets(){
		$widges = array();
		foreach (glob(M::app()->hook->widgetFolder.'*') as $item){
			if(is_dir($item)&&is_file($f=$item.DIRECTORY_SEPARATOR.'settings.php')!==false){
				$temp = include $f;
				$widges[] = array('title'=>$temp['title'],'desc'=>$temp['description']);
			}
		}
		return $widges;
	}

	
	/**
	 * 获取缓存清单
	 * @return array
	 */
	private function getCaches(){
		$cacheMenu = M::app()->cache->getCacheMenu();
		$menu = array();
		foreach($cacheMenu as $k=>$v){
			$temp = explode('|', $v);
			foreach ($temp as $i=>$item){
				switch ($i){
					case 0:
						$temp[$i]=($item>1000) ? ($item/1000).' KB':$item.' byte';
						break;
					case 1:
						$temp[$i]=date('Y-m-d H:i:s',$item);
						break;
					case 2:
						$temp[$i]=$this->tTime($k,$item);
						break;
				}
			}
			array_splice($temp, 0,0,$k);
			$menu[] = $temp;
		}
		return $menu;
	}
	
	/**
	 * 过期时间转换
	 * @param int $t
	 */
	private function tTime($k,$t){
		$_t = $t-time();
		$_t_ = '';
		if($_t>0){
			if($_t>60){
				$_delay = array('秒','分','小时','天','月','年');
				$delay = array(1,60,3600,86400,2592000,946080000);
				for($i=4;$i>=0;$i--){
					if($_t>=$delay[$i]){
						$d = floor($_t/$delay[$i]);
						$_t_.= $d.$_delay[$i];
						$_t-=$delay[$i]*$d;
					}
				}
			}else{
				$_t_.= $_t.'秒';
			}
		}else{
			$_t_ = '<span class="red">已過期</span>';
			//联动删除
			M::app()->cache->delete($k);
		}
		return $_t_;
	}
	
	/**
	 * 删除缓存
	 * @param string $f
	 */
	private function clearCache($f){
		$tip = $f.',缓存删除成功。';
		if($f=='all'){
			M::app()->cache->flush();
			$tip = '全部缓存清空。';
		}else
			M::app()->cache->delete($f);
		M::app()->session->message = $tip;
		M::app()->http->redirect('?v=cache-tool');
	}
	
	/**
	 * 创建widget
	 */
	public function widget(){
		if(M::app()->http->isPostRequest())
			$this->createWidget(M::app()->http->getReqData(array('folder','class','title','description'),'P'));
		M::app()->http->redirect('?v=widget-tool');
	}
	
	private function createWidget($data){
		$target = M::app()->hook->widgetFolder.$data['folder'];
		if(is_dir($target)){
			$tip ='组件目录已经存在。'; 
		}else{
			#创建目录
			mkdir($target,0777,true);
			$current = dirname(__FILE__).DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR;
			#创建文件
			$isOk = $this->createFiles($current,$target,$data);
			$tip = $isOk? '组件生成成功。':'组件生成文件失败';
		}
		#保存消息到session
		M::app()->session->message = $data['folder'].' '.$tip; 
	}
	
	private function createFiles($current,$target,$data){
		foreach ($data as $k=>$v){
			if(!$v)
				$data[$k] = $data['folder'];
		}		
		
		$data['class'] = ucfirst(strtolower($data['class']));
		#配置文件
		$content = $this->renderInternal($current.'settings.php',$data,true);
		@file_put_contents($target.DIRECTORY_SEPARATOR.'settings.php',$content);
		#类文件
		$content = $this->renderInternal($current.'class.php',$data,true);
		@file_put_contents($target.DIRECTORY_SEPARATOR.$data['class'].'.php',$content);
		#视图文件
		$content = $this->renderInternal($current.'_view.php',$data,true);
		@file_put_contents($target.DIRECTORY_SEPARATOR.'_view.php',$content);
		return true;
	}
}