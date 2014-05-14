<?php
/**
 * Header
 * Micro.widgets.header.Header
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Header extends Widget{
	
	public function run(){
		M::app()->seo->title = $this->title;
		return $this->settings;
	}	
}