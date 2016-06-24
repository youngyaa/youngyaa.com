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
$timeFormat = $this->config->event_time_format ? $this->config->event_time_format : 'g:i a' ;
$daysInWeek = array(
		0 => JText::_('EB_SUNDAY'),
		1 => JText::_('EB_MONDAY'),
		2 => JText::_('EB_TUESDAY'),
		3 => JText::_('EB_WEDNESDAY'),
		4 => JText::_('EB_THURSDAY'),
		5 => JText::_('EB_FRIDAY'),
		6 => JText::_('EB_SATURDAY')
);

$monthsInYear = array(
		1 => JText::_('EB_JAN'),
		2 => JText::_('EB_FEB'),
		3 => JText::_('EB_MARCH'),
		4 => JText::_('EB_APR'),
		5 => JText::_('EB_MAY'),
		6 => JText::_('EB_JUNE'),
		7 => JText::_('EB_JUL'),
		8 => JText::_('EB_AUG'),
		9 => JText::_('EB_SEP'),
		10 => JText::_('EB_OCT'),
		11 => JText::_('EB_NOV'),
		12 => JText::_('EB_DEC')
);
?>
<h1 class="eb-page-heading"><?php echo JText::_('EB_CALENDAR') ; ?></h1>
<div id="extcalendar">
<div style="width: 100%;" class="topmenu_calendar">
	<div class="left_calendar">
		<strong><?php echo JText::_('EB_CHOOSE_DATE'); ?>:</strong>
		<?php echo JHtml::_('calendar', JRequest::getVar('day', ''),'date', 'date', '%Y-%m-%d'); ?>
		<input type="button" class="btn" value="<?php echo JText::_('Go'); ?>" onclick="gotoDate();" />
	</div>
	<?php
		if ($this->showCalendarMenu)
		{
			echo EventbookingHelperHtml::loadCommonLayout('common/calendar_navigation.php', array('Itemid' => $this->Itemid, 'config' => $this->config, 'layout' => 'daily', 'currentDateData' => $this->currentDateData));
		}
	?>
</div>
<div class="wraptable_calendar">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr class="tablec">
		<td class="previousday">
			<a href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=daily&day=".date('Y-m-d',strtotime("-1 day", strtotime($this->day)))."&Itemid=$this->Itemid");?>" rel="nofollow">
				<img src="<?php echo JUri::root()?>media/com_eventbooking/assets/images/calendar_previous.png" alt="<?php echo JText::_('EB_PREVIOUS_DAY')?>">
			</a>
		</td>
		<td class="currentday currentdaytoday">
			<?php
				$time = strtotime($this->day) ;
				echo $daysInWeek[date('w', $time)].', '.$monthsInYear[date('n', $time)].' '.date('d', $time).', '.date('Y', $time);
			?>
		</td>
		<td class="nextday">
			<a href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=daily&day=".date('Y-m-d',strtotime("+1 day", strtotime($this->day)))."&Itemid=$this->Itemid");?>" rel="nofollow">
				<img src="<?php echo JUri::root()?>media/com_eventbooking/assets/images/calendar_next.png" alt="<?php echo JText::_('EB_NEXT_DAY')?>">
			</a>
		</td>
	</tr>

	<tr>
		<td colspan="3">
			<?php
			if (count($this->events))
			{
			?>
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<?php
					foreach ($this->events AS $key => $event)
					{
						$url = JRoute::_(EventbookingHelperRoute::getEventRoute($event->id, 0, $this->Itemid));
				?>
					<tr>
						<td class="tablea">
							<a href="<?php echo $url; ?>"><?php echo JHtml::_('date', $event->event_date, $timeFormat, null);?></a>
						</td>
						<td class="tableb">
							<div class="eventdesc">
								<h4><a href="<?php echo $url; ?>"><?php echo $event->title?></a></h4>
								<p class="location-name">
									<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id.'&tmpl=component&format=html'); ?>" rel="gb_page_center[500,450]" title="<?php echo $event->location_name ; ?>" class="location_link" rel="nofollow"><?php echo $event->location_name; ?></a>
							     </p>
								<?php echo $event->short_description; ?>
							</div>
						</td>
					</tr>
				<?php }?>
			</table>
			<?php
			}
			else
			{
				echo '<span class="eb_no_events">'.JText::_('EB_NO_EVENTS')."</span>";
			}
			?>
		</td>
	</tr>
</table>
</div>
</div>
<script type="text/javascript">
	var url = "<?php echo JRoute::_('index.php?option=com_eventbooking&view=calendar&layout=daily&Itemid='.$this->Itemid.'&day=', false); ?>";
	function gotoDate()
	{
		date = document.getElementById('date');
		if (date.value)
		{
			location.href = url + date.value;
		}
		else
		{
			alert("<?php echo JText::_('EB_PLEASE_CHOOSE_DATE'); ?>");
		}
	}
</script>