<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012-2014 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
if (!isset($categoryId))
{
	$categoryId = 0;
}
$span7Class    = $bootstrapHelper->getClassMapping('span7');
$span5class    = $bootstrapHelper->getClassMapping('span5');
$btnClass      = $bootstrapHelper->getClassMapping('btn');
$imgClass      = $bootstrapHelper->getClassMapping('img-polaroid');
$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
$nullDate      = JFactory::getDbo()->getNullDate();
$defaultItemId = $Itemid;
for ($i = 0 , $n = count($items) ;  $i < $n ; $i++)
{
	$item = $items[$i] ;
	$Itemid = OSMembershipHelperRoute::getPlanMenuId($item->id, $item->category_id, $defaultItemId);
	if ($item->thumb)
	{
		$imgSrc = JUri::base().'media/com_osmembership/'.$item->thumb ;
	}

	if ($item->category_id)
	{
		$url = JRoute::_('index.php?option=com_osmembership&view=plan&catid='.$item->category_id.'&id='.$item->id.'&Itemid='.$Itemid);
	}
	else
	{
		$url = JRoute::_('index.php?option=com_osmembership&view=plan&id='.$item->id.'&Itemid='.$Itemid);
	}

	if ($config->use_https)
	{
		$signUpUrl = JRoute::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid), false, 1);
	}
	else
	{
		$signUpUrl = JRoute::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid));
	}

	$symbol = $item->currency_symbol ? $item->currency_symbol : $item->currency;
	?>
	<div class="osm-item-wrapper clearfix">
		<div class="osm-item-heading-box clearfix">
			<h2 class="osm-item-title">
				<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
					<?php echo $item->title; ?>
				</a>
			</h2>
		</div>
		<div class="osm-item-description clearfix">
			<div class="<?php echo $rowFluidClass; ?>">
				<div class="osm-description-details <?php echo $span7Class; ?>">
					<?php
					if ($item->thumb)
					{
					?>
						<img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="osm-thumb-left <?php echo $imgClass; ?>"/>
					<?php
					}
					if ($item->short_description)
					{
						echo $item->short_description;
					}
					else
					{
						echo $item->description;
					}
					?>
				</div>
				<div class="<?php echo $span5class; ?>">
					<table class="table table-bordered table-striped">
						<?php
						if ($item->recurring_subscription && $item->trial_duration)
						{
						?>
							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo JText::_('OSM_TRIAL_DURATION'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									if ($item->lifetime_membership)
									{
										echo JText::_('OSM_LIFETIME');
									}
									else
									{
										switch ($item->trial_duration_unit) {
											case 'D' :
												echo $item->trial_duration.' '.JText::_('OSM_DAYS');
												break;
											case 'W' :
												echo $item->trial_duration.' '.JText::_('OSM_WEEKS');
												break;
											case 'M' :
												echo $item->trial_duration.' '.JText::_('OSM_MONTHS');
												break;
											case 'Y' :
												echo $item->trial_duration.' '.JText::_('OSM_YEARS');
												break;
											default :
												echo $item->trial_duration.' '.JText::_('OSM_DAYS');
												break;
										}
									}
									?>
								</td>
							</tr>

							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo JText::_('OSM_TRIAL_PRICE'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									if ($item->trial_amount > 0)
									{
										echo OSMembershipHelper::formatCurrency($item->trial_amount, $config, $symbol);
									}
									else
									{
										echo JText::_('OSM_FREE');
									}
									?>
								</td>
							</tr>
						<?php
						}
						if (!$item->expired_date || ($item->expired_date == $nullDate))
						{
						?>
							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo JText::_('OSM_DURATION'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									if ($item->lifetime_membership)
									{
										echo JText::_('OSM_LIFETIME');
									}
									else
									{
										$length = $item->subscription_length;
										switch ($item->subscription_length_unit) {
											case 'D':
												$text = $length > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY');
												break ;
											case 'W' :
												$text = $length > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK');
												break ;
											case 'M' :
												$text = $length > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH');
												break ;
											case 'Y' :
												$text = $length > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR');
												break ;
										}
										echo $item->subscription_length.' '.$text;
									}
									?>
								</td>
							</tr>
						<?php
						}
						?>
						<tr class="osm-plan-property">
							<td class="osm-plan-property-label">
								<?php echo JText::_('OSM_PRICE'); ?>:
							</td>
							<td class="osm-plan-property-value">
								<?php
								if ($item->price > 0)
								{
									echo OSMembershipHelper::formatCurrency($item->price, $config, $symbol);
								}
								else
								{
									echo JText::_('OSM_FREE');
								}
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="osm-taskbar clearfix">
				<ul>
					<?php
					if (OSMembershipHelper::canSubscribe($item))
					{
					?>
						<li>
							<a href="<?php echo $signUpUrl; ?>" class="<?php echo $btnClass; ?> btn-primary">
								<?php echo JText::_('OSM_SIGNUP'); ?>
							</a>
						</li>
					<?php
					}

					if (empty($config->hide_details_button))
					{
					?>
						<li>
							<a href="<?php echo $url; ?>" class="<?php echo $btnClass; ?>">
								<?php echo JText::_('OSM_DETAILS'); ?>
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