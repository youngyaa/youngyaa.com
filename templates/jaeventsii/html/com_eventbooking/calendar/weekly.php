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
$width = (int) $this->config->map_width ;
if (!$width)
{
	$width = 800 ;
}
$height = (int) $this->config->map_height ;
if (!$height)
{
	$height = 600 ;
}
EventbookingHelperJquery::colorbox('eb-colorbox-map', $width.'px', $height.'px', 'true', 'false');
$bootstrapHelper = new EventbookingHelperBootstrap($this->config->twitter_bootstrap_version);
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

<div class="eb-calendar-page">
	<h1 class="eb-page-heading"><?php echo JText::_('EB_CALENDAR') ; ?></h1>
	<div id="extcalendar">
	<div class="topmenu_calendar row">
		<div class="left_calendar col-sm-7">
			<div class="today">
				<?php
					$startWeekTime = strtotime("$this->first_day_of_week");
					$endWeekTime = strtotime("+6 day", strtotime($this->first_day_of_week)) ;
					echo $daysInWeek[date('w', $startWeekTime)].'. '.date('d', $startWeekTime).' '.$monthsInYear[date('n', $startWeekTime)].', '.date('Y', $startWeekTime).' - '.$daysInWeek[date('w', $endWeekTime)].'. '.date('d', $endWeekTime).' '.$monthsInYear[date('n', $endWeekTime)].', '.date('Y', $endWeekTime) ;
				?>
			</div>
		</div>
		<?php
		if ($this->showCalendarMenu)
		{
		?>
			<div class="col-sm-5">
				<?php echo EventbookingHelperHtml::loadCommonLayout('common/calendar_navigation.php', array('Itemid' => $this->Itemid, 'config' => $this->config, 'layout' => 'weekly', 'currentDateData' => $this->currentDateData)); ?>
			</div>
		<?php
		}
		?>
	</div>

	<div class="wraptable_calendar">
	<table cellpadding="0" cellspacing="0" width="100%" border="0">

		<tr class="tablec">
			<td class="previousweek">
				<a href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=weekly&date=".date('Y-m-d',strtotime("-7 day", strtotime($this->first_day_of_week)))."&Itemid=$this->Itemid"); ?>" rel="nofollow">
					<i class="fa fa-angle-double-left"></i>
				</a>
			</td>
			<td class="currentweek currentweektoday">
				<?php echo JText::_('EB_WEEK')?> <?php echo date('W',strtotime("+6 day", strtotime($this->first_day_of_week)));?>
			</td>
			<td class="nextweek">
				<a class="shajaxLinkNextWeek extcalendar prefetch" href="<?php echo JRoute::_("index.php?option=com_eventbooking&view=calendar&layout=weekly&date=".date('Y-m-d',strtotime("+7 day", strtotime($this->first_day_of_week)))."&Itemid=$this->Itemid"); ?>" rel="nofollow">
					<i class="fa fa-angle-double-right"></i>
				</a>
			</td>
		</tr>
		<?php
			foreach ($this->events AS $key => $events)
			{
			?>
			<tr class="tableh2">
				<td class="tableh2" colspan="3">
					<?php
						$time = strtotime("+$key day", strtotime($this->first_day_of_week)) ;
						echo $daysInWeek[date('w', $time)].'. '.date('d', $time).' '.$monthsInYear[date('n', $time)].', '.date('Y', $time) ;
					?>
				</td>
			</tr>
			<tr>
				<?php
					if (!count($events))
					{
					?>
					<td align="center" class="tableb table-none" colspan="3">
							<strong>
								<?php echo JText::_('EB_NO_EVENT_ON_THIS_DAY');?>
							</strong>
					</td>
					<?php
					}
					else
					{
					?>
					<td class="tableb table-none" align="left" colspan="3">
						 <?php
							foreach ($events as $event)
							{
								$url = JRoute::_(EventbookingHelperRoute::getEventRoute($event->id, 0, $this->Itemid));
						    ?>
							<table width="100%">
								<tr>
									<td class="tablea">
										<a href="<?php echo $url; ?>"><?php echo JHtml::_('date', $event->event_date, $timeFormat, null);?></a>
									</td>
									<td class="tableb">
										 <div class="eventdesc">
											<h4><a href="<?php echo $url; ?>"><?php echo $event->title?></a></h4>
											<p class="location-name">
												<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id.'&Itemid='.$this->Itemid.'&tmpl=component&format=html'); ?>" title="<?php echo $event->location_name ; ?>" class="eb-colorbox-map" rel="nofollow">
													<?php echo $event->location_name; ?>
												</a>
											</p>
											<?php echo $event->short_description; ?>
										</div>
									</td>
								</tr>
							</table>
							<?php
							}
						 ?>
					</td>
					<?php
					}
				?>
			</tr>
		<?php
			}
		?>
	</table>
	</div>
	</div>
</div>