<?php $this->widget('header',array(
		'title'=>'Widget Tool',
		'viewFile'=>'_tool',
		'description'=>'工具包 - 自动生成组件工具'));
?>
<div class="content">
<?php $this->widget('tools',array('viewFile'=>'WTool'));?>
<h3>调用</h3>
<pre class="code">
/**
 * 一行即可调用
 */
$this-&gt;widget('tools',array('viewFile'=&gt;'WTool'));
</pre>
</div>