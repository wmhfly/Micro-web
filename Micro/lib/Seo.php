<?php
/**
 * 页面seo信息
 * Micro.lib.Seo
 * ~~~~~~~~~~~~~~~~
 * @property string $poweredBy powered by 文本
 * @property array $meta 页面元标签 
 * @property string $title 页面元标签 标题
 * @property string $keywords 页面元标签 关键字
 * @property string $description 页面元标签 描述
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
class Seo extends Component {
	
	public $poweredBy = '';
	public $meta = array(
			'title'=>'',
			'keywords'=>'',
			'description'=>''
	);
	
	public function __get($k){
		return isset($this->meta[$k])? $this->meta[$k]:'';
	}
	
	public function __set($k,$v){
		if(isset($this->meta[$k]))
			$this->meta[$k] = $v;
	}
	
}