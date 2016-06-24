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
?>
<div class="eb-topmenu-calendar">
	<ul class="eb-menu-calendar nav nav-pills">
		<li>
			<?php
			$month = $currentDateData['month'];
			$year = $currentDateData['year'];
			?>
			<a class="calendar_link<?php if ($layout == 'default') echo ' active'; ?>" href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&month=$month&year=$year&Itemid=$Itemid"); ?>" class="calendar_link active" rel="nofollow">
				<?php echo JText::_('EB_MONTHLY_VIEW')?>
			</a>
		</li>
		<?php
		if ($config->activate_weekly_calendar_view)
		{
			$date = $currentDateData['start_week_date'];
		?>
			<li>
				<a class="calendar_link<?php if ($layout == 'weekly') echo ' active'; ?>" href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=weekly&date=$date&Itemid=$Itemid"); ?>" rel="nofollow">
					<?php echo JText::_('EB_WEEKLY_VIEW')?>
				</a>
			</li>
		<?php
		}
		if ($config->activate_daily_calendar_view)
		{
		?>
			<li>
				<?php $day = $currentDateData['current_date']; ?>
				<a class="calendar_link<?php if ($layout == 'daily') echo ' active'; ?>" href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=daily&day=$day&Itemid=$Itemid"); ?>" rel="nofollow">
					<?php echo JText::_('EB_DAILY_VIEW')?>
				</a>
			</li>
		<?php
		}
		?>
	</ul>
</div>