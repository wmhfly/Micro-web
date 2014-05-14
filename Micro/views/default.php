<?php 
M::app()->seo->title ='文档 documentation 1.0';
?>

<h1 id="logo">Micro-web Framework</h1>
<p class="desc"><span>“</span>PHP微框架，非MVC架构，基于组件挂载机制设计，内置HTTP请求，DB连接，Seo组件，Cache基本功能<span>”</span><p>
<div class="tag-list">
<a href="?v=folder-design"><i class="txt">目录结构</i></a>
<a href="?v=settings"><i class="txt">配置介绍</i></a>
<a href="?v=core-classes"><i class="txt">核心类</i></a>
<a href="?v=tools"><i class="txt">系统Tools包</i></a>
<a href="?v=hello-world"><i class="txt">Hello world</i></a>
<a target="_blank" href="http://wmhfly.com/micro-web/micro-web.zip"><i class="txt">Download</i></a>
<!-- <a target="_blank" href="https://github.com/wmhfly/Micro-web/"><i class="txt">Micro-web On Github</i></a> -->
<a target="_blank" href="http://wmhfly.com/about.html"><i class="txt">关于我</i></a>
</div>
<div class="content">
<h3>快速入门  <span>《Hello world demo》</span></h3>
<p><span class="hot">访问</span> http://you-domain/?v=hello-world</p>
<h5>目录关联文件</h5>
<pre class="code">
Micro
 |- views
 |   |- hello-world.php  模板文件
 |- widgets
 |   |- hello
 |       |- settings.php 配置文件
 |       |- Hello.php    组件类
 |       |- _view.php    视图文件
</pre>
<h5>关联文件代码code</h5>
<p><span class="hot">模板文件</span> hello-world.php</p>
<pre class="code">
/**
 * $this为Hook对象，组件加载器
 * $this-&gt;widget('组件的标识',array('组件配置，会覆盖settings.php'))
 */
$this-&gt;widget('hello');
</pre>
<p><span class="hot">配置文件</span> settings.php</p>
<pre class="code">
/**
 * 默认 &amp; 组件配置：
 * array(
 *  'class'=&gt;'hello',
 *  'viewFile'=&gt;'_view'
 *  ... orther diy config 
 * )
 */
return array(
    'title'=&gt;'hello',
    'description'=&gt;'hello world 组件'
);
</pre>
<p><span class="hot">组件类</span> Hello.php</p>
<pre class="code">
/**
 * 组件必须继承Widget类
 * 必须实现run抽象方法，且返回一个数组，推送数据到模板
 */
class Hello extends Widget{
  public function run(){
  	return array('title'=&gt;'hello world');
  }
}
</pre>
</div>