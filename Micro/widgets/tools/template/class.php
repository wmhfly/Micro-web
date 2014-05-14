<?php echo "<?php\n"; ?>
/**
 * <?php echo $title."\n"; ?>
 * Micro.widgets.<?php echo $folder ?>.<?php echo $class."\n"; ?>
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * @author Wu Miao Hui <363539981@qq.com>
 * @link http://www.wmhfly.com/
 * @version 1.0
 */
 class <?php echo $class ?> extends Widget {
 	public function run(){
 		return array('title'=>'<?php echo $title ?>');
 	}
 }