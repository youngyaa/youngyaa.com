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
defined( '_JEXEC' ) or die;
?>
<div class="btn-group">
	<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-plus"></i> <?php echo JText::_('EB_SAVE_TO'); ?> <span class="caret"></span></button>
	<ul class="dropdown-menu">
		<li><a class="google" href="<?php echo EventbookingHelperHtml::getAddToGoogleCalendarUrl($item); ?>" target="_blank"><?php echo JText::_('EB_GOOGLE_CALENDAR'); ?></a></li>
		<li><a class="yahoo" href="<?php echo EventbookingHelperHtml::getAddToYahooCalendarUrl($item);?>" target="_blank"><?php echo JText::_('EB_YAHOO_CALENDAR'); ?></a></li>
		<li><a class="download" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=event.download_ical&event_id='.$item->id.'&Itemid='.$Itemid); ?>"><i class="icon-download"></i> <?php echo JText::_('EB_DOWNLOAD_ICAL'); ?></a></li>
	</ul>
</div>