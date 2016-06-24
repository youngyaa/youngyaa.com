<?php
/**
 * @version        2.0.0
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('script', JUri::root().'media/com_eventbooking/assets/js/noconflict.js', false, false);	
if ($showLocation)
{
	$width = (int) $config->map_width ;
	if (!$width)
	{
		$width = 800 ;
	}
	$height = (int) $config->map_height ;
	if (!$height)
	{
		$height = 600 ;
	}
	EventbookingHelperJquery::colorbox('eb-colorbox-map', $width.'px', $height.'px', 'true', 'false');
}
if (count($rows))
{
    $monthNames = array(
        1 => JText::_('EB_JAN_SHORT'),
        2 => JText::_('EB_FEB_SHORT'),
        3 => JText::_('EB_MARCH_SHORT'),
        4 => JText::_('EB_APR_SHORT'),
        5 => JText::_('EB_MAY_SHORT'),
        6 => JText::_('EB_JUNE_SHORT'),
        7 => JText::_('EB_JULY_SHORT'),
        8 => JText::_('EB_AUG_SHORT'),
        9 => JText::_('EB_SEP_SHORT'),
        10 => JText::_('EB_OCT_SHORT'),
        11 => JText::_('EB_NOV_SHORT'),
        12 => JText::_('EB_DEC_SHORT')
    );
?>
    <div class="eb-event-list">
        <ul class="eventsmall">
            <?php
            $k = 0 ;
            foreach ($rows as  $row) {
                $k = 1 - $k ;
                $date = JHtml::_('date', $row->event_date, 'd', null);
                $month = JHtml::_('date', $row->event_date, 'n', null);
                ?>
                <li class="vevent clearfix row-fluid">
                    <div class="span3">
                        <span class="event-date">
                            <span title="">
                                <span class="month"><?php echo $monthNames[$month];?></span>
                                <span class="day"><?php echo $date; ?></span>
                            </span>
                        </span>
                    </div>
                    <div class="span9">
                    	<p>
                        <a class="url eb-event-link" href="<?php echo JRoute::_(EventbookingHelperRoute::getEventRoute($row->id, 0, $itemId), false); ?>">
                            <strong class="summary"><?php echo $row->title ; ?></strong>
                        </a>
                        </p>
                        <?php
                            if ($showCategory)
                            {
                            ?>
                                <p><small title="<?php echo JText::_('EB_CATEGORY'); ?>" class="category"><span>
									<i class="icon-folder-open"></i>
									<?php //echo JText::_('EB_CATEGORY'); ?><?php echo $row->categories ; ?></span></small></p>
                            <?php
                            }
                            if ($showLocation && strlen($row->location_name))
                            {
                            ?>
                                <p><small title="<?php echo JText::_('EB_LOCATION'); ?>" class="location">
								<i class="icon-map-marker"></i>
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$row->location_id.'&tmpl=component&format=html&Itemid='.$itemId); ?>" class="eb-colorbox-map"								
								<strong><?php echo $row->location_name ; ?></strong>
                                </a></small></p>
                            <?php
                            }
                            ?>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php
}
else
{
?>
    <div class="eb_empty"><?php echo JText::_('EB_NO_UPCOMING_EVENTS') ?></div>
<?php
}
?>