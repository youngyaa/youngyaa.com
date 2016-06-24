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
if ($this->month == 12)
{
	$nextMonth = 1 ;
	$nextYear = $this->year + 1 ;
	$previousMonth = 11 ;
	$previousYear = $this->year ;
}
elseif ($this->month == 1)
{
	$nextMonth = 2 ;
	$nextYear = $this->year ;
	$previousMonth = 12 ;
	$previousYear = $this->year - 1 ;
}
else
{
	$nextMonth = $this->month + 1 ;
	$nextYear = $this->year ;
	$previousMonth = $this->month - 1 ;
	$previousYear = $this->year ;
}
?>
<script src="<?php echo JURI::base(true) . '/media/com_eventbooking/assets/js/jquery.equalheights.js'; ?>" type="text/javascript"></script>
<div id="eb-category-calendar-page" class="eb-container row-fluid">
	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=category&layout=calendar&id='.$this->category->id.'&Itemid='.$this->Itemid);?>">
		<?php
		if ($this->config->show_cat_decription_in_calendar_layout)
		{
		?>
			<div id="eb-category">
				<h1 class="eb-page-heading"><?php echo $this->category->name;?></h1>
				<?php
				if($this->category->description != '')
				{
				?>
					<div class="eb-description"><?php echo $this->category->description;?></div>
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		<?php
		}
		?>
		<div id="eb-calendarwrap">
			<?php
			echo EventbookingHelperHtml::loadCommonLayout('common/calendar.php',
				array(
					'Itemid' => $this->Itemid,
					'config' => $this->config,
					'previousMonth' => $previousMonth,
					'nextMonth' => $nextMonth,
					'previousMonthLink' => JRoute::_('index.php?option=com_eventbooking&view=category&layout=calendar&id='.$this->category->id.'&month='.$previousMonth.'&year='.$previousYear.'&Itemid='.$this->Itemid),
					'nextMonthLink' => JRoute::_('index.php?option=com_eventbooking&view=category&layout=calendar&id='.$this->category->id.'&month='.$nextMonth.'&year='.$nextYear.'&Itemid='.$this->Itemid),
					'listMonth' => $this->listMonth,
					'searchMonth' => $this->searchMonth,
					'searchYear' => $this->searchYear,
					'data'    => $this->data,
					'categoryId' => $this->category->id
				));
			?>
		</div>
	</form>
</div>