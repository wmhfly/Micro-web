<style type="text/css">
.tab {width:100%; color:#666}
.tab th{font-size:16px; color:#c2c2c2; text-align:left;padding-left:15px;}
.tab th a {font-weight:normal;}
.tab tr {margin-bottom:5px;}
.tab td {border-top:1px solid #ececec;border-right:1px solid #ececec;border-bottom:1px solid #ececec;padding:10px 10px 10px 15px;}
.tab tr td.s {border-left:4px solid #26a8e2;}
.tab tr.s_1 td.s {border-left-color:#f2ae43;}
.tab tr.s_2 td.s {border-left-color:#034498;}
.tab tr.s_3 td.s {border-left-color:#27b779;}
.tab tr.s_4 td.s {border-left-color:#e7191b;}
.tab tr.selected td,.tab tr.hover td{background-color:#ececec}
</style>
<?php if(($message=M::app()->session->getFlash('message'))!==null):?>
<p class="tip"><?php echo $message;?></p>
<?php endif;?>
<table class="tab">
  <tr>
    <th>缓存标识</th>
    <th>大小</th>
    <th>开始</th>
    <th>有效期</th>
    <th><a href="?v=cache-tool&f=all"  onclick="if(!confirm('是否删除全部缓存文件?!')) return false;">清空缓存</a></th>
  </tr>
<?php foreach ($list as $index=>$item):?>
  <tr class="<?php echo 's_'.($index<5?$index:$index % 5)?>">
    <td class="s"><?php echo $item[0]?></td>
    <td><?php echo $item[1]?></td>
    <td><?php echo $item[2]?></td>
    <td><?php echo $item[3]?></td>
    <td><a href="?v=cache-tool&f=<?php echo $item[0];?>" onclick="if(!confirm('【<?php echo $item[0]?>】是否确定删除?!')) return false;">delete</a></td>
  </tr>
<?php endforeach;?> 
<?php if(!$list):?>
<tr>
<td colspan="5"> No caches data.</td>
</tr>
<?php endif;?>
</table>
