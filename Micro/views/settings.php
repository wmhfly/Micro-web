<?php $this->widget('header',array(
		'title'=>'配置介绍',
		'description'=>'Micro-web可细分为全局配置(含核心类初始化配置)，组件配置'));
?>
<h3>全局配置</h3>
<pre class="code">
Micro
 |- widgets
 |   |- settings.php 全局配置文件
</pre>
<pre class="code">
/**
 * 配置文件
 * Micro.widgets.settings
 * ~~~~~~~~~~~~~~~~~~~~~~~~
 * 配置说明
 * 	filter: 初始化应用时不会被载入
 *
 * 核心类初始化配置
 *	在核心类中的功能属性，都可以在这里初始化设置
 */
return array(
	#应用名称
	'app_name'=&gt;'Micro-web',
	#过滤核心类(db,http,session,cache,hook,seo)
	'filter'=&gt;array('db'),
	#数据库配置(PDO类库驱动)
	'db'=&gt;array(
		'connectionString'=&gt;'mysql:host=localhost;dbname=***',
		'username'=&gt;'',
		'password'=&gt;''
	),
	#组件加载器配置
	'hook'=&gt;array(
		#缓存级别(1为组件缓存；2为页面缓存；默认为0，不缓存)
		'cache_level'=&gt;2,
	),
	... orther diy
);
</pre>
<h3>组件配置 </h3>
<p><span class="hot">hello</span> 组件为例：</p>
<pre class="code">
Micro
 |- widgets
 |   |- hello
 |       |- settings.php 配置文件
 |       |- Hello.php 组件类
 |       |- _view.php 视图文件
</pre>
<pre class="code">
/**
 * hello
 * Micro.widgets.hello.settings
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 全部组件都存在2个隐藏配置，如果没有显示配置，则默认为：
 *  class: Hello
 *  viewFile: _view
 */
return array(
	'title'=&gt;'hello',
	'description'=&gt;'hello world demo'
);
</pre>
<pre class="code">
$this-&gt;widget('hello',array('特殊配置，会覆盖settings.php配置'));
</pre>