<?php $this->widget('header',array(
		'title'=>'Cache Tool',
		'viewFile'=>'_tool',
		'description'=>'工具包 - 缓存工具包'));
?>
<div class="content">
<?php $this->widget('tools',array('viewFile'=>'CTool'));?>
<h3>调用</h3>
<pre class="code">
/**
 * 一行即可调用
 */
$this-&gt;widget('tools',array('viewFile'=&gt;'CTool'));
</pre>
</div>