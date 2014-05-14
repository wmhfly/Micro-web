<?php
/**
 * POST 请求处理
 * Micro.widgets.handler.Handler
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 默认的POST请求模块
 * eg:
 * 	  POST request uri => /?act=***&args
 * 
 * eg:
 *    自定义POST 请求模块
 *    ajax request uri => /?v=widget_name&args
 * 
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
 class Handler extends Widget {
 	public function run(){
 		return array('title'=>'handler');
 	}
 }