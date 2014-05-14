<?php
/**
 * XMLHttpRequest 异步请求
 * Micro.widgets.async.Async
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 默认的ajax请求模块
 * eg:
 * 	  ajax request uri => /?act=***&args
 * 
 * eg:
 *    自定义ajax 请求模块
 *    ajax request uri => /?v=widget_name&args
 * 
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Async extends Widget{
	
	public function run(){
		$result = '';
		switch (M::app()->http->action){
			default:
				$result = 'Micro-web async api';
		}
		return $result;
	}	
}