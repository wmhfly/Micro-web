<?php $this->widget('header',array(
		'title'=>'Widgets manage',
		'viewFile'=>'_tool',
		'description'=>'工具包 - 组件管理工具'));
?>
<div class="content">
<?php $this->widget('tools',array('viewFile'=>'MTool'));?>
<h3>调用</h3>
<pre class="code">
/**
 * 一行即可调用
 */
$this-&gt;widget('tools',array('viewFile'=&gt;'MTool'));
</pre>
</div>