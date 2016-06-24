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
$return = base64_encode(JUri::getInstance()->toString());
?>
<div id="eb-events">
	<?php
		$activateWaitingList = $config->activate_waitinglist_feature;
		$rowFluidClass       = $bootstrapHelper->getClassMapping('row-fluid');
		$span7Class          = $bootstrapHelper->getClassMapping('span7');
		$span5Class          = $bootstrapHelper->getClassMapping('span5');
		$btnClass            = $bootstrapHelper->getClassMapping('btn');
		$iconPencilClass     = $bootstrapHelper->getClassMapping('icon-pencil');
		$iconOkClass    = $bootstrapHelper->getClassMapping('icon-ok');
		$iconRemoveClass  = $bootstrapHelper->getClassMapping('icon-remove');
		$iconDownloadClass     = $bootstrapHelper->getClassMapping('icon-download');
		for ($i = 0 , $n = count($events) ;  $i < $n ; $i++)
		{
			$event = $events[$i] ;
			$canRegister = EventbookingHelper::acceptRegistration($event);
			$detailUrl = JRoute::_(EventbookingHelperRoute::getEventRoute($event->id, @$category->id, $Itemid));
			if (($event->event_capacity > 0) && ($event->event_capacity <= $event->total_registrants) && $activateWaitingList && !@$event->user_registered && $event->number_event_dates > 0)
			{
				$waitingList = true ;
			}
			else
			{
				$waitingList = false ;
			}
		?>
			<div class="eb-event clearfix">
				<div class="eb-description clearfix">
					<div class="row">
					<div class="eb-description-details col-sm-7">

						<div class="eb-box-heading clearfix">
							<h2 class="eb-event-title pull-left">
								<a href="<?php echo $detailUrl; ?>" title="<?php echo $event->title; ?>" class="eb-event-title-link">
									<?php echo $event->title; ?>
								</a>
							</h2>
						</div>
						<?php
							if ($event->thumb && file_exists(JPATH_ROOT.'/media/com_eventbooking/images/thumbs/'.$event->thumb)) {
							?>
								<a href="<?php echo JUri::base(true).'/media/com_eventbooking/images/'.$event->thumb; ?>" class="eb-modal"><img src="<?php echo JUri::base(true).'/media/com_eventbooking/images/thumbs/'.$event->thumb; ?>" class="eb-thumb-left"/></a>
							<?php
							}
							//output event description
							if (!$event->short_description)
							{
								$event->short_description = $event->description;
							}
							echo $event->short_description;
						?>
					</div>
					<div class="col-sm-5">
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
							if ($event->registration_start_date != $nullDate)
							{
								?>
								<tr class="eb-event-property">
									<td class="eb-event-property-label">
										<?php echo JText::_('EB_REGISTRATION_START_DATE'); ?>
									</td>
									<td class="eb-event-property-value">
										<?php echo JHtml::_('date', $event->registration_start_date, $config->event_date_format, null) ; ?>
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
										<?php echo JText::_('EB_CAPACTIY'); ?>
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
							if ($config->show_registered && $event->registration_type != 3)
							{
							?>
								<tr class="eb-event-property">
									<td class="eb-event-property-label">
										<?php echo JText::_('EB_REGISTERED'); ?>
									</td>
									<td class="eb-event-property-value">
										<?php
											echo (int) $event->total_registrants;
											if ($config->show_list_of_registrants && ($event->total_registrants > 0) && EventbookingHelper::canViewRegistrantList())
											{
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
								$showPrice = true ;
							}
							else
							{
								$showPrice = false ;
							}
							if ($config->show_discounted_price && ($event->individual_price > $event->discounted_price))
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
													if ($event->early_bird_discount_date != $nullDate)
													{
														echo ' <em> '.JText::sprintf('EB_UNTIl_DATE', JHtml::_('date', $event->early_bird_discount_date, $config->date_format, null)).'</em>';
													}
												}
												else
												{
													echo '<span class="eb_price">'.JText::_('EB_FREE').'</span>' ;
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
													echo '<span class="eb_price">'.JText::_('EB_FREE').'</span>' ;
												}
											?>
										</td>
									</tr>
								<?php
								}
							}
							if ($event->fixed_group_price > 0)
							{
								?>
								<tr>
									<td>
										<strong><?php echo JText::_('EB_FIXED_GROUP_PRICE'); ?></strong>
									</td>
									<td class="eb_price">
										<?php
											echo EventbookingHelper::formatCurrency($event->fixed_group_price, $config, $event->currency_symbol) ;
										?>
									</td>
								</tr>
							<?php
							}


							if ($event->late_fee > 0)
							{
							?>
								<tr class="eb-event-property">
									<td class="eb-event-property-label">
										<?php echo JText::_('EB_LATE_FEE'); ?>
									</td>
									<td class="eb-event-property-value">
										<?php
											echo EventbookingHelper::formatCurrency($event->late_fee, $config, $event->currency_symbol);
											echo ' <em> '.JText::sprintf('EB_FROM_DATE', JHtml::_('date', $event->late_fee_date, $config->date_format, null)).'</em>';
										?>
									</td>
								</tr>
							<?php
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
										<strong><?php echo JText::_('EB_LOCATION'); ?></strong>
									</td>
									<td class="eb-event-property-value">
										<?php
											if ($event->location_address)
											{
											?>
												<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$event->location_id.'&tmpl=component'); ?>" class="eb-colorbox-map"><?php echo $event->location_name ; ?></a>
											<?php
											}
											else
											{
												echo $event->location_name;
											}
										?>
									</td>
								</tr>
							<?php
							}
							if ($event->attachment && !empty($config->show_attachment_in_frontend))
							{
								?>
								<tr>
									<td>
										<strong><?php echo JText::_('EB_ATTACHMENT'); ?></strong>
									</td>
									<td>
										<a href="<?php echo JUri::base().'/media/com_eventbooking/'.$event->attachment?>"><?php echo $event->attachment; ?></a>
									</td>
								</tr>
							<?php
							}
							?>
						</table>
					</div>
				</div>
				<?php
				if (!$canRegister && $event->registration_type != 3 && $config->display_message_for_full_event && !$waitingList && $event->registration_start_minutes >= 0)
				{
					if (@$event->user_registered)
					{
						$msg = JText::_('EB_YOU_REGISTERED_ALREADY');
					}
					elseif (!in_array($event->registration_access, $viewLevels))
					{
						$msg = JText::_('EB_LOGIN_TO_REGISTER') ;
					}
					else
					{
						$msg = JText::_('EB_NO_LONGER_ACCEPT_REGISTRATION') ;
					}
				?>
					<div class="clearfix">
						<p class="text-info eb-notice-message"><?php echo $msg; ?></p>
					</div>
				<?php
				}
				?>
				<div class="eb-taskbar clearfix">
					<ul>
						<?php
							if ($canRegister)
							{
								$registrationUrl = trim($event->registration_handle_url);
								if ($registrationUrl)
								{
								?>
									<li>
										<a class="<?php echo $btnClass; ?>" href="<?php echo $registrationUrl; ?> btn-inverse" target="_blank"><?php echo JText::_('EB_REGISTER_GROUP');; ?></a>
									</li>
								<?php
								}
								else
								{
									if ($event->registration_type == 0 || $event->registration_type == 1)
									{
										if ($config->multiple_booking)
										{
											$url        = 'index.php?option=com_eventbooking&task=cart.add_cart&id=' . (int) $event->id . '&Itemid=' . (int) $Itemid;
											$extraClass = 'eb-colorbox-addcart';
											$text       = JText::_('EB_REGISTER');
										}
										else
										{
											$url        = JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $event->id . '&Itemid=' . $Itemid, false, $ssl);
											$text       = JText::_('EB_REGISTER_INDIVIDUAL');
											$extraClass = '';
										}
										?>
										<li>
											<a class="<?php echo $btnClass.' '.$extraClass;?> btn-inverse"
											   href="<?php echo $url; ?>"><?php echo $text; ?></a>
										</li>
									<?php
									}
									if (($event->registration_type == 0 || $event->registration_type == 2) && !$config->multiple_booking)
									{
									?>
										<li>
											<a class="<?php echo $btnClass; ?> btn-inverse" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id='.$event->id.'&Itemid='.$Itemid, false, $ssl) ; ?>"><?php echo JText::_('EB_REGISTER_GROUP');; ?></a>
										</li>
									<?php
									}
								}
							}
							elseif ($waitingList)
							{
								if ($event->registration_type == 0 || $event->registration_type == 1)
								{
									?>
									<li>
										<a class="<?php echo $btnClass; ?> btn-inverse" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id='.$event->id.'&Itemid='.$Itemid, false, $ssl);?>"><?php echo JText::_('EB_REGISTER_INDIVIDUAL_WAITING_LIST'); ; ?></a>
									</li>
								<?php
								}
								if (($event->registration_type == 0 || $event->registration_type == 2) && !$config->multiple_booking)
								{
									?>
									<li>
										<a class="<?php echo $btnClass; ?> btn-inverse" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id='.$event->id.'&Itemid='.$Itemid, false, $ssl) ; ?>"><?php echo JText::_('EB_REGISTER_GROUP_WAITING_LIST'); ; ?></a>
									</li>
								<?php
								}
							}
							if ($config->show_save_to_personal_calendar)
							{
							?>
								<li>
									<?php echo EventbookingHelperHtml::loadCommonLayout('common/save_calendar.php', array('item' => $event, 'Itemid' => $Itemid)); ?>
								</li>
							<?php
							}
							$registrantId = EventbookingHelper::canCancelRegistration($event->id) ;
							if ($registrantId !== false)
							{
							?>
								<li>
									<a class="<?php echo $btnClass; ?> btn-default" href="javascript:cancelRegistration(<?php echo $registrantId; ?>)"><?php echo JText::_('EB_CANCEL_REGISTRATION'); ?></a>
								</li>
							<?php
							}
							if (EventbookingHelper::checkEditEvent($event->id))
							{
								?>
								<li>
									<a class="<?php echo $btnClass; ?> btn-default" href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=event&layout=form&id='.$event->id.'&Itemid='.$Itemid.'&return='.$return); ?>">
										<i class="<?php echo $iconPencilClass; ?>"></i>
										<?php echo JText::_('EB_EDIT'); ?>
									</a>
								</li>
							<?php
							}
							if (EventbookingHelper::canChangeEventStatus($event->id))
							{
								if ($event->published == 1)
								{
									$link = JRoute::_('index.php?option=com_eventbooking&task=event.unpublish&id='.$event->id.'&Itemid='.$Itemid.'&return='.$return);
									$text = JText::_('EB_UNPUBLISH');
									$class = $iconRemoveClass;
								}
								else
								{
									$link = JRoute::_('index.php?option=com_eventbooking&task=event.publish&id='.$event->id.'&Itemid='.$Itemid.'&return='.$return);
									$text = JText::_('EB_PUBLISH');
									$class = $iconOkClass;
								}
								?>
								<li>
									<a class="<?php echo $btnClass; ?> btn-default" href="<?php echo $link; ?>">
										<i class="<?php echo $class; ?>"></i>
										<?php echo $text; ?>
									</a>
								</li>
							<?php
							}

							if ($event->total_registrants && EventbookingHelper::canExportRegistrants($event->id))
							{
							?>
								   <li>
										<a class="<?php echo $btnClass; ?> btn-default" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.export&event_id='.$event->id.'&Itemid='.$Itemid); ?>">
											<i class="<?php echo $iconDownloadClass; ?>"></i>
											<?php echo JText::_('EB_EXPORT_REGISTRANTS'); ?>
										</a>
								   </li>
							<?php
							}
							if ($config->hide_detail_button !== '1')
							{
							?>
								<li>
									<a class="<?php echo $btnClass; ?> btn-primary" href="<?php echo $detailUrl; ?>">
										<?php echo JText::_('EB_DETAILS'); ?>
									</a>
								</li>
							<?php
							}
						?>
					</ul>
					</div>
				</div>
			</div>
		<?php
		}
	?>
</div>

<script type="text/javascript">
	function cancelRegistration(registrantId) {
		var form = document.adminForm ;
		if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
			form.task.value = 'registrant.cancel' ;
			form.id.value = registrantId ;
			form.submit() ;
		}
	}
</script>