<title><?php echo $metas['title'].' - '.M::app()->seo->poweredBy;?></title>
<?php foreach($metas as $k=>$v):?>
<meta name="<?php echo $k;?>" content="<?php echo $v;?>">
<?php endforeach;?>
