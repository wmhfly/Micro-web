<?php
/**
 * 配置文件
 * Micro.widgets.settings
 * ~~~~~~~~~~~~~~~~~~~~~~~~
 * 配置说明：
 * 	filter: 初始化应用过滤载入核心类
 * 
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
return array(
	#应用名称
	'app_name'=>'Micro-web',
	'filter'=>array(),
	#数据库配置
	'db'=>array(
		'connectionString'=>'mysql:host=localhost;dbname=***',
		'username'=>'',
		'password'=>''
	),
	'hook'=>array(
		#缓存配置
		'cache_level'=>2,
	),
	'seo'=>array(
		'poweredBy'=>'Micro-web',
		'meta'=>array(
			'title'=>'Micro-web',
			'keywords'=>'Micro-web,php,widgets挂载,web Firamework',
			'description'=>'PHP微框架，非MVC架构，基于组件挂载机制设计，内置HTTP请求，DB连接，Seo组件，Cache基本功能'
		)
	)
);