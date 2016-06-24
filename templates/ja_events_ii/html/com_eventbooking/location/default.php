<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

$width = (int) $this->config->map_width ;
if (!$width)
{
	$width = 500 ;
}
$height = (int) $this->config->map_height ;
if (!$height)
{
	$height = 450 ;
}
$param = null ;
if ($this->config->use_https)
{
	$ssl = 1;
}
else
{
	$ssl = 0;
}
EventbookingHelperJquery::colorbox('eb-colorbox-map', $width.'px', $height.'px', 'true', 'false');
$getDirectionLink = 'http://maps.google.com/maps?f=d&daddr='.$this->location->lat.','.$this->location->long.'('.addslashes($this->location->address.', '.$this->location->city.', '.$this->location->state.', '.$this->location->zip.', '.$this->location->country).')' ;
JHtml::_('behavior.modal', 'a.eb-modal');
?>
<div class="eb-location-page">
	<h1 class="eb-page-heading"><?php echo JText::sprintf('EB_EVENTS_FROM_LOCATION', $this->location->name); ?><a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$this->location->id.'&tmpl=component&format=html'); ?>"  title="<?php echo $this->location->name ; ?>" class="eb-colorbox-map view_map_link"><?php echo JText::_('EB_VIEW_MAP'); ?></a><a class="view_map_link" href="<?php echo $getDirectionLink ; ?>" target="_blank"><?php echo JText::_('EB_GET_DIRECTION'); ?></a></h1>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=location&location_id='.$this->location->id.'&Itemid='.$this->Itemid) ; ?>">
		<?php
		if (count($this->items))
		{
			echo EventbookingHelperHtml::loadCommonLayout('common/events_default.php', array('events' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate , 'param' => $param, 'ssl' => $ssl, 'width' => $width, 'height' => $height , 'viewLevels' => $this->viewLevels, 'bootstrapHelper' => $this->bootstrapHelper));
		}
		else 
		{
		?>
			<p class="text-info"><?php echo JText::_('EB_NO_EVENTS_FOUND') ?></p>
		<?php	
		}
		if ($this->pagination->total > $this->pagination->limit)
		{
		?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php
		}
		?>
	</form>
</div>