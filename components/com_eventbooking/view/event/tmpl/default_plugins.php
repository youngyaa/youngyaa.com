<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

if (count($this->plugins) > 1)
{
?>
	<div id="plugin_tab">
		<ul class="nav nav-tabs">
			<!-- Plugin support -->
			<?php
			if (count($this->plugins)) {
				$count = 0 ;
				foreach ($this->plugins as $plugin) {
					$title  = $plugin['title'] ;
					$count++ ;
					if($count == 1)$class = 'active';
					else $class = '';
					?>
					<li class="<?php echo $class; ?>"><a href="#<?php echo 'tab_'.$count;  ?>" data-toggle="tab"><?php echo $title;?></a></li>
				<?php
				}
			}
			?>
		</ul>
		<!-- Plugin support -->
		<div class="tab-content">
			<?php
			if (count($this->plugins)) {
				$count = 0 ;
				foreach ($this->plugins as $plugin) {
					$form = $plugin['form'] ;
					$count++ ;
					if($count == 1)$class = 'active';
					else $class = '';
					?>
					<div class="tab-pane <?php echo $class; ?>" id="tab_<?php echo $count; ?>">
						<?php
						echo $form ;
						?>
					</div>
				<?php
				}
			}
			?>
		</div>
	</div>
<?php
}
else
{
	$plugin = $this->plugins[0];
?>
	<div id="eb-plugins">
		<h3><?php echo $plugin['title']; ?></h3>
		<div class="eb-plugin-output">
			<?php echo $plugin['form']; ?>
		</div>
		<div class="clearfix"></div>
	</div>
<?php
}
?>