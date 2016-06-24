<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
$item = $this->item;
if ($item->thumb)
{
	$imgSrc = JUri::base() . 'media/com_osmembership/' . $item->thumb;
}
if ($this->config->use_https)
{
	$signUpUrl = JRoute::_(OSMembershipHelperRoute::getSignupRoute($item->id, $this->Itemid), false, 1);
}
else
{
	$signUpUrl = JRoute::_(OSMembershipHelperRoute::getSignupRoute($item->id, $this->Itemid));
}
$pageHeading = $this->params->get('page_heading') ? $this->params->get('page_heading') : $item->title;
$nullDate    = JFactory::getDbo()->getNullDate();
$symbol = $item->currency_symbol ? $item->currency_symbol : $item->currency;
?>
<div id="osm-plan-item" class="osm-container">
	<div class="osm-item-heading-box clearfix">
		<h1 class="osm-page-title">
			<?php echo $pageHeading; ?>
		</h1>
	</div>
	<div class="osm-item-description clearfix">
			<div class="<?php echo $this->bootstrapHelper->getClassMapping('row-fluid'); ?> clearfix">
				<div class="osm-description-details <?php echo $this->bootstrapHelper->getClassMapping('span7'); ?> ">
					<?php
					if ($item->thumb)
					{
					?>
						<img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="osm-thumb-left img-polaroid"/>
					<?php
					}
					if ($item->description)
					{
						echo $item->description;
					}
					else
					{
						echo $item->short_description;
					}
					?>
				</div>
				<div class="<?php echo $this->bootstrapHelper->getClassMapping('span5'); ?>">
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
										switch ($item->trial_duration_unit)
										{
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
										echo OSMembershipHelper::formatCurrency($item->trial_amount, $this->config, $symbol);
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
									echo OSMembershipHelper::formatCurrency($item->price, $this->config, $symbol);
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
							<a href="<?php echo $signUpUrl; ?>" class="<?php echo $this->bootstrapHelper->getClassMapping('btn'); ?> btn-primary">
								<?php echo JText::_('OSM_SIGNUP'); ?>
							</a>
						</li>
					<?php
					}
					?>
				</ul>
			</div>
		</div>
</div>