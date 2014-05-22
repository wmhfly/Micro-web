<?php echo "<?php\n"; ?>
/**
 * <?php echo $title."\n"; ?>
 * Micro.widgets.<?php echo $folder ?>.settings
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
return array(
	<?php if($folder!=strtolower($class)):?>
	'class'=>'<?php echo $class?>',
	<?php endif;?>
	'title'=>'<?php echo $title ?>',
	'description'=>'<?php echo $description ?>',
);