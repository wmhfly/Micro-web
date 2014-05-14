<?php $this->widget('header',array(
		'title'=>'框架核心类',
		'description'=>'Micro-web 核心类介绍'));
?>
<h3>核心类列表</h3>
<pre class="code">
Micro
|- lib
|  |- Micro/M/App/Component/Widget   基类
|  |- HttpRequest  http请求类
|  |- HttpSession  session类
|  |- DbConnection 基于PDO连接数据库操作类
|  |- FileCache    文件缓存类
|  |- Hook         模板和组件加载器
|  |- Seo          页面title，keywords，description管理
</pre>
<p><span class="hot">全局可访问核心类实例</span> M 即为 Micro，控制App实例，而app则关联了其他核心类</p>
<pre class="code">
/**
 * Micro:app()即为App的实例
 */
M:app()-&gt;http
M:app()-&gt;session
M:app()-&gt;db
M:app()-&gt;cache
M:app()-&gt;hook
M:app()-&gt;seo
</pre>
<dl class="dl">
  <dt>http请求类</dt>
  <dd>HttpRequest请求过滤封装，参数v为模板标识。</dd>
  <dt>session类</dt>
  <dd>HttpSession操作，带有M:app()-&gt;session-&gt;getFlash($k)消息闪现。</dd>
  <dt>http和session的便捷操作</dt>
  <dd>
<pre class="code">
#HttpRequest和HttpSession都有_get($k)，而session还有_set($k,$v)
$v = M:app()-&gt;http-&gt;v;

#session
M:app()-&gt;session-&gt;user = 'admin';
$user = M:app()-&gt;session-&gt;user;
</pre>
  </dd>
  <dt>数据库操作类</dt>
  <dd>DbConnection <a target="_blank" href="http://wmhfly.com/PDO-DbConnection/">文档详细介绍</a></dd>
  <dt>文件缓存类</dt>
  <dd>FileCache 可以配置缓存路径，文件后缀，缓存清单文件。</dd>
  <dd>自带有垃圾回收机制</dd>
  <dt>模板和组件加载器</dt>
  <dd>Hook 可以配置模板，组件文件夹路径，缓存级别，缓存时间，默认layout，以及默认模板</dd>
  <dd>负责载入模板，挂载模板中的组件，缓存控制等</dd>
  <dd>小技巧：如果模板文件命名是以“_”开始，则不会载入layout模板</dd>
  <dt>Seo类</dt>
  <dd>页面title，keywords，description管理,版权poweredBy控制</dd>
</dl>
