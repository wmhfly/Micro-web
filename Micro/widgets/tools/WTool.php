<?php if(($message=M::app()->session->getFlash('message'))!==null):?>
<p class="tip"><?php echo $message;?></p>
<?php endif;?>
<form method="post" name="tool" action="?v=tools&act=widget" onsubmit="return chkForm(this);">
	<p><span class="hot">提示</span> /Micro/widgets/*** 下自动生成：组件目录、主类、视图、配置等文件。</p>
	<ul class="flist">
		 <li><span>组件目录: <cite>*</cite></span> <input type="text" name="folder"> <i class="s">必填，英文字母组成，不能为中文</i></li>
		 <li><span>组件主类: </span><input type="text" name="class"> <i class="s">为空则和组件文件夹相同</i></li>
		 <li><span>组件名称: </span><input type="text" name="title"> <i class="s">名称标识</i></li>
		 <li><span>组件描述: </span><input type="text" name="description"> <i class="s">组件介绍描述，方便后台管理</i></li>
	</ul>
	<p class="btn"><input type="submit" name="submit" value="提交" /> <input type="reset" name="reset" value="重置" /></p>
</form>
<script type="text/javascript">
(function(d){
	var iptEls = d.getElementsByTagName('input'),
		i=0,
		l=iptEls.length;
	for(;i<l;i++){
		if(iptEls[i].getAttribute('type')=='text'){
			iptEls[i].onfocus = function(){
				this.setAttribute('class','focus');
	        }
			iptEls[i].onblur = function(){
				this.setAttribute('class','');
			}
		}
	}
		
})(document);
function chkForm(o){
	if(!o.folder.value || /.*[\u4e00-\u9fa5]+.*$/.test(o.folder.value) ){
		alert('组件目录不能为空,或者包含中文字符');
		o.folder.focus();
		return false;
	}
	return true;
}
</script>