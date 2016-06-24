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
$activateWaitingList = $config->activate_waitinglist_feature;
$hiddenPhoneClass    = $bootstrapHelper->getClassMapping('hidden-phone');
$btnClass            = $bootstrapHelper->getClassMapping('btn');
?>
<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
		<?php
			if ($config->show_image_in_table_layout)
			{
			?>
				<th class="hidden-phone">
					<?php echo JText::_('EB_EVENT_IMAGE'); ?>
				</th>
			<?php
			}
		?>
		<th>
			<?php echo JText::_('EB_EVENT_TITLE'); ?>
		</th>
		<th class="date_col">
			<?php echo JText::_('EB_EVENT_DATE'); ?>
		</th>
		<?php
			if ($config->show_event_end_date_in_table_layout)
			{
			?>
				<th class="date_col <?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_EVENT_END_DATE'); ?>
				</th>
			<?php
			}
			if ($config->show_location_in_category_view)
			{
			?>
				<th class="location_col <?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_LOCATION'); ?>
				</th>
			<?php
			}
			if ($config->show_price_in_table_layout)
			{
			?>
				<th class="table_price_col <?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_INDIVIDUAL_PRICE'); ?>
				</th>
			<?php
			}
			if ($config->show_capacity)
			{
			?>
				<th class="capacity_col <?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_CAPACITY'); ?>
				</th>
			<?php
			}
			if ($config->show_registered)
			{
			?>
				<th class="registered_col <?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_REGISTERED'); ?>
				</th>
			<?php
			}
			if ($config->show_available_place)
			{
			?>
				<th class="center available-place-col <?php echo $hiddenPhoneClass; ?>">
					<?php echo JText::_('EB_AVAILABLE_PLACE'); ?>
				</th>
			<?php
			}
			?>
			<th class="center actions-col <?php echo $hiddenPhoneClass; ?>">
				<?php echo JText::_('EB_REGISTER'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
		for ($i = 0 , $n = count($items) ; $i < $n; $i++)
		{
			$item = $items[$i] ;
			$canRegister = EventbookingHelper::acceptRegistration($item) ;
			if (($item->event_capacity > 0) && ($item->event_capacity <= $item->total_registrants) && $activateWaitingList && !$item->user_registered && $item->number_event_dates > 0)
			{
				$waitingList = true ;
			}
			else
			{
				$waitingList = false ;
			}
		?>
			<tr>
				<?php
					if ($config->show_image_in_table_layout)
					{
					?>
					<td class="eb-image-column <?php echo $hiddenPhoneClass; ?>">
						<?php
							if ($item->thumb)
							{
							?>
								<a href="<?php echo JUri::base(true).'/media/com_eventbooking/images/'.$item->thumb; ?>" class="eb-modal"><img src="<?php echo JUri::base(true).'/media/com_eventbooking/images/thumbs/'.$item->thumb; ?>" class="eb_thumb-left"/></a>
							<?php
							}
							else
							{
								echo ' ';
							}
						?>
					</td>
					<?php
					}
				?>
				<td>
					<a href="<?php echo JRoute::_(EventbookingHelperRoute::getEventRoute($item->id, $categoryId, $Itemid));?>" class="eb-event-link"><?php echo $item->title ; ?></a>
				</td>
				<td>
					<?php
						if ($item->event_date == EB_TBC_DATE)
						{
							echo JText::_('EB_TBC');
						}
						else
						{
							echo JHtml::_('date', $item->event_date, $config->event_date_format, null);
						}
					?>
				</td>
				<?php
					if ($config->show_event_end_date_in_table_layout)
					{
					?>
						<td>
							<?php 
								if ($item->event_end_date == EB_TBC_DATE)
								{
									echo JText::_('EB_TBC');
								}
								else
								{
									echo JHtml::_('date', $item->event_end_date, $config->event_date_format, null);
								}
							?>
						</td>
					<?php
					}
					if ($config->show_location_in_category_view)
					{
					?>
					<td class="<?php echo $hiddenPhoneClass; ?>">
						<?php
							if ($item->location_id)
							{
								if ($item->location_address)
								{
								?>
									<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$item->location_id.'&Itemid='.$Itemid.'&tmpl=component'); ?>" class="eb-colorbox-map"><?php echo $item->location_name ; ?></a>
								<?php
								}
								else
								{
									echo $item->location_name;
								}
							}
							else
							{
							?>
								&nbsp;
							<?php
							}
						?>
					</td>
					<?php
					}
					if ($config->show_price_in_table_layout)
					{
						if ($config->show_discounted_price)
						{
							$price = $item->discounted_price ;
						}
						else
						{
							$price = $item->individual_price ;
						}
					?>
						<td class="<?php echo $hiddenPhoneClass; ?>">
							<?php echo EventbookingHelper::formatCurrency($price, $config, $item->currency_symbol); ?>
						</td>
					<?php
					}
					if ($config->show_capacity)
					{
					?>
						<td class="center <?php echo $hiddenPhoneClass; ?>">
							<?php
								if ($item->event_capacity)
								{
									echo $item->event_capacity ;
								}
								else
								{
									echo JText::_('EB_UNLIMITED') ;
								}
							?>
						</td>
					<?php
					}
					if ($config->show_registered)
					{
					?>
						<td class="center <?php echo $hiddenPhoneClass; ?>">
							<?php
								if ($item->registration_type != 3)
								{
									echo $item->total_registrants ;
								}
								else
								{
									echo ' ';
								}

							?>
						</td>
					<?php
					}
					if ($config->show_available_place)
					{
					?>
						<td class="center <?php echo $hiddenPhoneClass; ?>">
							<?php
								if ($item->event_capacity)
								{
									echo $item->event_capacity - $item->total_registrants;
								}
							?>
						</td>
					<?php
					}
				?>
					<td class="center <?php echo $hiddenPhoneClass; ?>">
						<?php
							if ($waitingList || $canRegister || ($item->registration_type != 3 && $config->display_message_for_full_event))
							{
								if ($canRegister)
								{
								?>
								<div class="eb-taskbar">
									<ul>
										<?php
											$registrationUrl = trim($item->registration_handle_url);
											if ($registrationUrl)
											{
											?>
												<li>
													<a class="<?php echo $btnClass; ?>" href="<?php echo $registrationUrl; ?>" target="_blank"><?php echo JText::_('EB_REGISTER');; ?></a>
												</li>
											<?php
											}
											else
											{
												if ($item->registration_type == 0 || $item->registration_type == 1)
												{
													if ($config->multiple_booking)
													{
														$url        = 'index.php?option=com_eventbooking&task=cart.add_cart&id=' . (int) $item->id . '&Itemid=' . (int) $Itemid;
														if ($item->event_password)
														{
															$extraClass = '';
														}
														else
														{
															$extraClass = 'eb-colorbox-addcart';
														}
														$text       = JText::_('EB_REGISTER');
													}
													else
													{
														$url        = JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id=' . $item->id . '&Itemid=' . $Itemid, false, $ssl);
														$text       = JText::_('EB_REGISTER_INDIVIDUAL');
														$extraClass = '';
													}
													?>
													<li>
														<a class="<?php echo $btnClass . ' ' . $extraClass;?>"
														   href="<?php echo $url; ?>"><?php echo $text; ?></a>
													</li>
												<?php
												}
												if (($item->registration_type == 0 || $item->registration_type == 2) && !$config->multiple_booking)
												{
												?>
													<li>
														<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id='.$item->id.'&Itemid='.$Itemid, false, $ssl) ; ?>"><?php echo JText::_('EB_REGISTER_GROUP');; ?></a>
													</li>
												<?php
												}
											}
										?>
									</ul>
								</div>
								<?php
								}
								elseif($waitingList)
								{
								?>
								<div class="eb-taskbar">
									<ul>
										<?php
										if ($item->registration_type == 0 || $item->registration_type == 1)
										{
											?>
											<li>
												<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.individual_registration&event_id='.$item->id.'&Itemid='.$Itemid, false, $ssl);?>"><?php echo JText::_('EB_REGISTER_INDIVIDUAL_WAITING_LIST'); ; ?></a>
											</li>
										<?php
										}
										if (($item->registration_type == 0 || $item->registration_type == 2) && !$config->multiple_booking)
										{
											?>
											<li>
												<a class="<?php echo $btnClass; ?>" href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=register.group_registration&event_id='.$item->id.'&Itemid='.$Itemid, false, $ssl) ; ?>"><?php echo JText::_('EB_REGISTER_GROUP_WAITING_LIST'); ; ?></a>
											</li>
										<?php
										}
										?>
									</ul>
								</div>
								<?php
								}
								elseif($item->registration_type != 3 && $config->display_message_for_full_event && !$waitingList && $item->registration_start_minutes >= 0)
								{
									if (@$item->user_registered)
									{
										$msg = JText::_('EB_YOU_REGISTERED_ALREADY');
									}
									elseif (!in_array($item->registration_access, $viewLevels))
									{
										$msg = JText::_('EB_LOGIN_TO_REGISTER') ;
									}
									else
									{
										$msg = JText::_('EB_NO_LONGER_ACCEPT_REGISTRATION') ;
									}
								?>
									<div class="eb-notice-message">
										<?php echo $msg ; ?>
									</div>
								<?php
								}
							}
						?>
					</td>
			</tr>
			<?php
		}
	?>
	</tbody>
</table>