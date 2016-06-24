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
if (version_compare(JVERSION, '3.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}
$return = base64_encode(JUri::getInstance()->toString());
?>

<div class="my-events-page">
	<h1 class="eb-page-heading"><?php echo JText::_('EB_USER_EVENTS'); ?></h1>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=events&Itemid='.$this->Itemid); ; ?>">
		<table width="100%" class="table-filter">
			<tr>
				<td align="left">
					<?php echo JText::_( 'EB_FILTER' ); ?>:
					<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->lists['filter_search'];?>" class="input-medium text_area search-query" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();" class="btn btn-primary"><?php echo JText::_( 'EB_GO' ); ?></button>
				</td >
				<td style="float: right;">
					<?php echo $this->lists['filter_category_id'] ; ?>
				</td>
			</tr>
		</table>
		<?php
		if(count($this->items))
		{
		?>
			<table class="table table-striped table-bordered table-considered">
				<thead>
					<tr>
						<th>
							<?php echo JText::_('EB_TITLE'); ?>
						</th>
						<th width="18%">
							<?php echo JText::_('EB_CATEGORY'); ?>
						</th>
						<th class="center" width="10%">
							<?php echo JText::_('EB_EVENT_DATE'); ?>
						</th>
						<th width="7%">
							<?php echo JText::_('EB_NUMBER_REGISTRANTS'); ?>
						</th>
						<th width="5%" nowrap="nowrap">
							<?php echo JText::_('EB_STATUS'); ?>
						</th>
						<th width="1%" nowrap="nowrap">
							<?php echo JText::_('EB_ID'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getPagesLinks(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$k = 0;
					for ($i=0, $n=count( $this->items ); $i < $n; $i++)
					{
						$row = $this->items[$i];
						$link 	= JRoute::_(EventbookingHelperRoute::getEventRoute($row->id, 0, $this->Itemid));
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td>
								<a href="<?php echo $link; ?>" target="_blank">
									<?php echo $row->title ; ?>
								</a>
								<span class="action-link">
									<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=event&layout=form&id='.$row->id.'&Itemid='.$this->Itemid.'&return='.$return); ?>"><i class="icon-pencil"></i> <?php echo JText::_('EB_EDIT'); ?></a>
									<?php
									if ($row->published == 1)
									{
										$link = JRoute::_('index.php?option=com_eventbooking&task=event.unpublish&id='.$row->id.'&Itemid='.$this->Itemid.'&return='.$return);
										$text = JText::_('EB_UNPUBLISH');
										$class = 'fa fa-minus-circle';
									}
									else
									{
										$link = JRoute::_('index.php?option=com_eventbooking&task=event.publish&id='.$row->id.'&Itemid='.$this->Itemid.'&return='.$return);
										$text = JText::_('EB_PUBLISH');
										$class = 'fa fa-check-circle';
									}
									?>
									<a href="<?php echo $link ; ?>"><i class="<?php echo $class;?>"></i> <?php echo $text ; ?></a>
								</span>
							</td>
							<td>
								<?php echo $row->category_name ; ?>
							</td>
							<td class="center">
								<?php echo JHtml::_('date', $row->event_date, $this->config->date_format, null); ?>
							</td>
							<td class="center">
								<?php echo (int) $row->total_registrants ; ?>
							</td>
							<td class="center">
								<?php
									if ($row->published)
									{
										echo JText::_('EB_PUBLISHED');
									}
									else
									{
										echo JText::_('EB_UNPUBLISHED');
									}
								?>
							</td>
							<td class="center">
								<?php echo $row->id; ?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
				</tbody>
			</table>
		<?php
		}
		?>
	</form>
</div>