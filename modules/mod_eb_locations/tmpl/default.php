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

if (count($rows))
{
?>
	<ul class="menu location_list">
		<?php
			foreach ($rows  as $row)
			{
	    		$link = JRoute::_('index.php?option=com_eventbooking&view=location&location_id='.$row->id.'&Itemid='.$itemId);		    		
			?>
				<li>
					<a href="<?php echo $link; ?>"><?php echo $row->name; ?>
						<?php
                                if ($showNumberEvents)
                                {
                                ?>
                                	<span class="number_events">&nbsp;(&nbsp;<?php echo $row->total_events .' '. ($row->total_events > 1 ? JText::_('EB_EVENTS') : JText::_('EB_EVENT')) ?>&nbsp;)&nbsp;</span>
                                <?php    
                                }
						    ?>
					</a>						
				</li>
			<?php	
			}
		?>			
	</ul>
<?php
}
?>					

