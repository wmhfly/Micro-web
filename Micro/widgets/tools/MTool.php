<style type="text/css">
.tab {width:100%; color:#666}
.tab th{font-size:16px; color:#c2c2c2; text-align:left;padding-left:15px;}
.tab th.s {width:50px;}
.tab th a {font-weight:normal;}
.tab tr {margin-bottom:5px;}
.tab td {border-top:1px solid #ececec;border-right:1px solid #ececec;border-bottom:1px solid #ececec;padding:10px 10px 10px 15px;}
.tab tr.N td {background-color:#fee}
.tab tr td.s {border-left:4px solid #26a8e2;}
.tab tr.s_1 td.s {border-left-color:#f2ae43;}
.tab tr.s_2 td.s {border-left-color:#034498;}
.tab tr.s_3 td.s {border-left-color:#27b779;}
.tab tr.s_4 td.s {border-left-color:#e7191b;}
.tab tr.hover td{background-color:#ffc;color:#000}
.tab td a {float:right;margin-top:2px; background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAAFCSURBVDhPrVLNSsNAEN438FzBd/Dmxerdi4/hG/gIvoHXgq1FL4oKLQWjVEWU4sFLT0oRhGrzU5M2adNssp+zmzTFmkIrfvCRDTPfzHyzy/4NQQSYI4Hkd34IADsNF9edYDFxRMrdZw+saKD2EWAQCnhE9eUCMp6k/gTFYuGhAXZsYunEQu7MwvJ5zNyphacuzxYXWj511JVQ8WiKVPTR5BjSFNJaIovRp9ZbdSftnIrGxcoGmk4I7TPA/svw9wSy6vZtD6xkoPzm492L0HIn9Ml0tU2LPNBRongim0Am5C9tNCyO136IDTpvXjmKa3ReueimNopZBXpkoeNHuNGpi7QxtjBthSasyEmyIK9rpph2sK7Zsx+THQjc6Rz3RswH2vZec6AK5jUHcsIkdT5U2iOsVr/gLiqUkNf1p3efDca+AXMbWI6JId7RAAAAAElFTkSuQmCC") no-repeat;width:15px; height:15px;}
</style>
<?php if(($message=M::app()->session->getFlash('message'))!==null):?>
<p class="tip"><?php echo $message;?></p>
<?php endif;?>
<table id="tablist" class="tab">
  <tr>
  	<th>组件包</th>
    <th>标题</th>
    <th>描述</th>
    <th class="s">启用</th>
  </tr>
<?php foreach ($list as $index=>$item):?>
  <tr class="<?php echo 's_'.($index<5?$index:$index % 5)?> <?php echo $item['status']?>">
    <td class="s"><?php echo $item['package']?></td>
    <td><?php echo $item['title']?></td>
    <td><?php echo $item['desc']?></td>
    <td> <?php if($item['system']):?>系统<?php else :?><?php echo $item['status']?><a href="?v=widget-manage&p=<?php echo $item['package']?>&stu=<?php echo $item['status']?>" title="切换">&nbsp;</a><?php endif;?></td>
  </tr>
<?php endforeach;?> 
<?php if(!$list):?>
<tr>
<td colspan="3"> No caches data.</td>
</tr>
<?php endif;?>
</table>
<script type="text/javascript">
(function(D){
	var trlist = D.getElementById('tablist').getElementsByTagName('tr');
	for(var i=0,l=trlist.length;i<l;i++){
		trlist[i].onmouseover = function(){
			this.className+=' hover';
		};
		trlist[i].onmouseout = function(){
			this.className=(this.className).replace(' hover','');
		};
	}
})(document);
</script>