<?php $this->widget('header',array(
		'title'=>'目录结构',
		'description'=>'Micro-web 物理目录设计介绍'));
?>
<h3>框架物理目录</h3>
<pre class="code">
index.php 入口文件
cache     缓存目录
Micro     框架目录
|- lib        框架核心类
|- extensions 扩展目录
|- views      引导模板
|- widgets    组件目录
</pre>
<dl class="dl">
  <dt>缓存目录</dt>
  <dd>缓存清单文件和缓存文件存放位置,Micro-web 内置缓存机制，可配置为组件模块级缓存，或者页面级缓存</dd>
  <dt>框架核心类</dt>
  <dd>Micro-web驱动类目录，包含Micro基类，以及数据库连接类，http，session，cache，hook，seo功能实现</dd>
  <dt>扩展目录</dt>
  <dd>存放第三方类库，如PHPMailer，验证码类等</dd>
  <dt>引导模板</dt>
  <dd><span class="hot">http://domain/?v=hello-word</span> 存放URL路由映射的hello-word.php模板文件，而模板中可以任意挂载不同组件</dd>
  <dt>组件目录</dt>
  <dd>各种功能模块组件位置</dd>
</dl>