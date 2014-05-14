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
    <th>组件</th>
    <th>描述</th>
  </tr>
<?php foreach ($list as $index=>$item):?>
  <tr class="<?php echo 's_'.($index<5?$index:$index % 5)?>">
    <td class="s"><?php echo $item['title']?></td>
    <td><?php echo $item['desc']?></td>
  </tr>
<?php endforeach;?> 
<?php if(!$list):?>
<tr>
<td colspan="3"> No caches data.</td>
</tr>
<?php endif;?>
</table>
