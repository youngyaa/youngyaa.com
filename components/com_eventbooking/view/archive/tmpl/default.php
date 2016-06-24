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
JHtml::_('behavior.modal', 'a.eb-modal');
$nullDate = JFactory::getDbo()->getNullDate();
?>
<div id="eb-events-archive-page" class="eb-container">
<h1 class="eb-page-heading"><?php echo JText::_('EB_EVENTS_ARCHIVE'); ?></h1>
<?php
if ($this->category)
{
?>
	<div id="eb-category">
		<h2 class="eb-page-heading"><?php echo $this->category->name;?></h2>
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

if(count($this->items))
{
	$rowFluidClass = $this->bootstrapHelper->getClassMapping('row-fluid');
	$span7Class    = $this->bootstrapHelper->getClassMapping('span7');
	$span5Class    = $this->bootstrapHelper->getClassMapping('span5');
	$btnClass      = $this->bootstrapHelper->getClassMapping('btn');
?>
	<div id="eb-events">
	<?php
		for ($i = 0 , $n = count($this->items) ;  $i < $n ; $i++)
		{
			$event = $this->items[$i] ;
			$url = JRoute::_(EventbookingHelperRoute::getEventRoute($event->id, 0, $this->Itemid));
		?>
			<div class="eb-event">
				<div class="eb-box-heading clearfix">
					<h3 class="eb-event-title pull-left">
						<a href="<?php echo $url; ?>" title="<?php echo $event->title; ?>" class="eb-event-title-link">
							<?php echo $event->title; ?>
						</a>
					</h3>
				</div>
				<div class="eb-description">
					<div class="<?php echo $rowFluidClass; ?>">
						<div class="eb-description-details <?php echo $span7Class; ?>">
							<?php
							if ($event->thumb && file_exists(JPATH_ROOT.'/media/com_eventbooking/images/thumbs/'.$event->thumb))
							{
							?>
								<a href="<?php echo JUri::base().'media/com_eventbooking/images/'.$event->thumb; ?>" class="eb-modal"><img src="<?php echo JUri::base().'media/com_eventbooking/images/thumbs/'.$event->thumb; ?>" class="eb-thumb-left"/></a>
							<?php
							}
							//output event description
							if (!$event->short_description)
							{
								$event->short_description = $event->description ;
							}
							echo $event->short_description ;
							?>
						</div>
						<div class="<?php echo $span5Class; ?>">
							<table class="table table-bordered table-striped">
								<tr class="eb-event-property">
									<td class="eb-event-property-label">
										<?php echo JText::_('EB_EVENT_DATE'); ?>
									</td>
									<td class="eb-event-property-value">
										<?php
										if ($event->event_date == EB_TBC_DATE)
										{
											echo JText::_('EB_TBC');
										}
										else
										{
											echo JHtml::_('date', $event->event_date, $config->event_date_format, null) ;
										}
										?>
									</td>
								</tr>
								<?php
								if ($event->event_end_date != $nullDate)
								{
								?>
									<tr class="eb-event-property">
										<td class="eb-event-property-label">
											<?php echo JText::_('EB_EVENT_END_DATE'); ?>
										</td>
										<td class="eb-event-property-value">
											<?php echo JHtml::_('date', $event->event_end_date, $config->event_date_format, null) ; ?>
										</td>
									</tr>
								<?php
								}
								if ($event->cut_off_date != $nullDate)
								{
								?>
									<tr class="eb-event-property">
										<td class="eb-event-property-label">
											<?php echo JText::_('EB_CUT_OFF_DATE'); ?>
										</td>
										<td class="eb-event-property-value">
											<?php echo JHtml::_('date', $event->cut_off_date, $config->event_date_format, null) ; ?>
										</td>
									</tr>
								<?php
								}
								if ($config->show_capacity)
								{
								?>
									<tr class="eb-event-property">
										<td class="eb-event-property-label">
											<?php echo JText::_('EB_CAPACTIY'); ?>:
										</td>
										<td class="eb-event-property-value">
											<?php
											if ($event->event_capacity)
											{
												echo $event->event_capacity ;
											}
											else
											{
												echo JText::_('EB_UNLIMITED') ;
											}
											?>
										</td>
									</tr>
								<?php
								}
								if ($config->show_registered)
								{
								?>
									<tr class="eb-event-property">
										<td class="eb-event-property-label">
											<?php echo JText::_('EB_REGISTERED'); ?>
										</td>
										<td class="eb-event-property-value">
											<?php echo (int) $event->total_registrants ; ?>
											<?php
											if ($config->show_list_of_registrants && ($event->total_registrants > 0) && EventbookingHelper::canViewRegistrantList()) {
											?>
												&nbsp;&nbsp;&nbsp;<a href="index.php?option=com_eventbooking&view=registrantlist&id=<?php echo $event->id ?>&tmpl=component" class="eb-colorbox-register-lists"><span class="view_list"><?php echo JText::_("EB_VIEW_LIST"); ?></span></a>
											<?php
											}
											?>
										</td>
									</tr>
								<?php
								}
								if ($config->show_available_place && $event->event_capacity)
								{
								?>
									<tr class="eb-event-property">
										<td class="eb-event-property-label">
											<?php echo JText::_('EB_AVAILABLE_PLACE'); ?>
										</td>
										<td class="eb-event-property-value">
											<?php echo $event->event_capacity - $event->total_registrants ; ?>
										</td>
									</tr>
								<?php
								}
								if (($event->individual_price > 0) || ($config->show_price_for_free_event))
								{
									$showPrice = true;
								}
								else
								{
									$showPrice = false;
								}
								if ($config->show_discounted_price && ($event->individual_price != $event->discounted_price))
								{
									if ($showPrice)
									{
									?>
										<tr class="eb-event-property">
											<td class="eb-event-property-label">
												<?php echo JText::_('EB_ORIGINAL_PRICE'); ?>
											</td>
											<td class="eb-event-property-value">
												<?php
												if ($event->individual_price > 0)
												{
													echo EventbookingHelper::formatCurrency($event->individual_price, $config, $event->currency_symbol);
												}
												else
												{
													echo '<span class="eb_price">'.JText::_('EB_FREE').'</span>' ;
												}
												?>
											</td>
										</tr>
										<tr class="eb-event-property">
											<td class="eb-event-property-label">
												<?php echo JText::_('EB_DISCOUNTED_PRICE'); ?>
											</td>
											<td class="eb-event-property-value">
												<?php
												if ($event->discounted_price > 0)
												{
													echo EventbookingHelper::formatCurrency($event->discounted_price, $config, $event->currency_symbol);
												}
												else
												{
													echo '<span class="eb_price">' . JText::_('EB_FREE') . '</span>';
												}
												?>
											</td>
										</tr>
									<?php
									}
								}
								else
								{
									if ($showPrice)
									{
									?>
										<tr class="eb-event-property">
											<td class="eb-event-property-label">
												<?php echo JText::_('EB_INDIVIDUAL_PRICE'); ?>
											</td>
											<td class="eb-event-property-value">
												<?php
												if ($event->individual_price > 0)
												{
													echo EventbookingHelper::formatCurrency($event->individual_price, $config, $event->currency_symbol);
												}
												else
												{
													echo '<span class="eb_price">' . JText::_('EB_FREE') . '</span>';
												}
												?>
											</td>
										</tr>
									<?php
									}
								}
								if (isset($event->paramData))
								{
									foreach ($event->paramData as $paramItem)
									{
										if ($paramItem['value'])
										{
										?>
											<tr class="eb-event-property">
												<td class="eb-event-property-label">
													<?php echo $paramItem['title']; ?>
												</td>
												<td class="eb-event-property-value">
													<?php
													echo $paramItem['value'];
													?>
												</td>
											</tr>
										<?php
										}
										?>
									<?php
									}
								}
								if ($event->location_id && $config->show_location_in_category_view)
								{
								?>
									<tr class="eb-event-property">
										<td class="eb-event-property-label">
											<strong><?php echo JText::_('EB_LOCATION'); ?>:</strong>
										</td>
										<td class="eb-event-property-value">
											<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id); ?>" class="eb-colorbox-map"><?php echo $event->location_name ; ?></a>
										</td>
									</tr>
								<?php
								}
								?>
							</table>
						</div>
					</div>
					<div class="eb-taskbar clearfix">
						<ul>
							<li>
								<a class="<?php echo $btnClass; ?> btn-primary" href="<?php echo $url; ?>">
									<?php echo JText::_('EB_DETAILS'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		<?php
		}
		?>
	</div>
	<?php
	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>
<?php
}
?>
</div>