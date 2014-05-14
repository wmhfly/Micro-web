<?php
/**
 * 组件顶级类
 * Micro.lib.Component
 * ~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Component {
	
	/**
	 * 初始化类配置文件
	 * @param array $config
	 */
	public function __construct($config = '') {
		if(is_array($config)){
			foreach ($config as $k=>$v){
				if(property_exists($this,$k))
					$this->$k = $v;
			}
		}
	}
}

?>