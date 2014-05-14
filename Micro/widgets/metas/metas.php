<?php
/**
 * Metas
 * Micro.widgets.metas.Metas
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Metas extends Widget {
	
	public function run(){
		return array('metas'=>M::app()->seo->meta);
	}
}