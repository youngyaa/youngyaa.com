<?php
for ($i = 0 , $n = count($items) ; $i < $n ; $i++)
{
	$item = $items[$i] ;
	$link = JRoute::_(OSMembershipHelperRoute::getCategoryRoute($item->id, $Itemid));
	?>
	<div class="osm-item-wrapper clearfix">
		<div class="osm-item-heading-box">
			<h3 class="osm-item-title">
				<a href="<?php echo $link; ?>" class="osm-item-title-link">
					<?php echo $item->title;?>
				</a>
				<small>( <?php echo $item->total_plans ;?> <?php echo $item->total_plans > 1 ? JText::_('OSM_PLANS') :  JText::_('OSM_PLAN') ; ?> )</small>
			</h3>
		</div>
		<?php
		if($item->description)
		{
		?>
			<div class="osm-item-description clearfix">
				<?php echo $item->description;?>
			</div>
		<?php
		}
		?>
	</div>
<?php
}