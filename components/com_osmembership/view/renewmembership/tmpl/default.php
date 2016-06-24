<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$db = JFactory::getDbo();
$query = $db->getQuery(true);
OSMembershipHelperJquery::validateForm();
?>
<div id="osm-profile-page" class="osm-container">
<h1 class="osm-page-title"><?php echo JText::_('OSM_RENREW_MEMBERSHIP'); ?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_osmembership&task=register.process_renew_membership&Itemid='.$this->Itemid, false, $this->config->use_https ? 1 : 0); ?>" method="post" name="osm_form_renew" id="osm_form_renew" autocomplete="off" class="form form-horizontal">
	<?php
	if (count($this->planIds))
	{
	?>
		<p class="osm-description"><?php echo JText::_('OSM_RENREW_MEMBERSHIP_DESCRIPTION'); ?></p>
		<ul class="osm-renew-options">
			<?php
			$renewOptionCount = 0;
			$fieldSuffix = OSMembershipHelper::getFieldSuffix();
			foreach ($this->planIds as $planId)
			{
				// Get the plan information
				$query->clear();
				$query->select('*')
					->from('#__osmembership_plans')
					->where('id = '. $planId);
				if ($fieldSuffix)
				{
					OSMembershipHelperDatabase::getMultilingualFields($query, array('title'), $fieldSuffix);
				}
				$db->setQuery($query);
				$plan = $db->loadObject();
				$symbol = $plan->currency_symbol ? $plan->currency_symbol : $plan->currency;

				$query->clear();
				$query->select('*')
					->from('#__osmembership_renewrates')
					->where('plan_id = '. $planId)
					->order('number_days');
				$db->setQuery($query);
				$renewOptions = $db->loadObjectList();

				if (count($renewOptions))
				{
					foreach ($renewOptions as $renewOption)
					{
						$renewOptionCount++;
						list($renewOptionFrequency, $renewOptionLength) = OSMembershipHelper::getRecurringSettingOfPlan($renewOption->number_days);
						switch ($renewOptionFrequency)
						{
							case 'D':
								$text = $renewOptionLength > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY');
								break ;
							case 'W' :
								$text = $renewOptionLength > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK');
								break ;
							case 'M' :
								$text = $renewOptionLength > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH');
								break ;
							case 'Y' :
								$text = $renewOptionLength > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR');
								break ;
						}

					?>
						<li class="osm-renew-option">
							<input type="radio" class="validate[required] inputbox" id="renew_option_id_<?php echo $renewOptionCount; ?>" name="renew_option_id" value="<?php echo $planId.'|'.$renewOption->id; ?>" />
							<label for="renew_option_id_<?php echo $renewOptionCount; ?>"><?php JText::printf('OSM_RENEW_OPTION_TEXT', $plan->title, $renewOptionLength.' '. $text, OSMembershipHelper::formatCurrency($renewOption->price, $this->config, $symbol)); ?></label>
						</li>
					<?php
					}
				}
				else
				{
					$renewOptionCount++;
					$length = $plan->subscription_length;
					switch ($plan->subscription_length_unit)
					{
						case 'D':
							$text = $length > 1 ? JText::_('OSM_DAYS') : JText::_('OSM_DAY');
							break;
						case 'W' :
							$text = $length > 1 ? JText::_('OSM_WEEKS') : JText::_('OSM_WEEK');
							break;
						case 'M' :
							$text = $length > 1 ? JText::_('OSM_MONTHS') : JText::_('OSM_MONTH');
							break;
						case 'Y' :
							$text = $length > 1 ? JText::_('OSM_YEARS') : JText::_('OSM_YEAR');
							break;
					}
					?>
					<li class="osm-renew-option">
						<input type="radio" class="validate[required] inputbox" id="renew_option_id_<?php echo $renewOptionCount; ?>" name="renew_option_id" value="<?php echo $planId;?>" />
						<label for="renew_option_id_<?php echo $renewOptionCount; ?>"><?php JText::printf('OSM_RENEW_OPTION_TEXT', $plan->title, $length.' '.$text, OSMembershipHelper::formatCurrency($plan->price, $this->config, $symbol)); ?></label>
					</li>
				<?php
				}
			}
			?>
		</ul>
		<div class="form-actions">
			<input type="submit" class="<?php echo $this->bootstrapHelper->getClassMapping('btn'); ?> btn-primary" value="<?php echo JText::_('OSM_PROCESS_RENEW'); ?>"/>
		</div>
	<?php
	}
	else
	{
	?>
		<p class="text-info"><?php echo JText::_('OSM_NO_RENEW_OPTIONS_AVAILABLE'); ?></p>
	<?php
	}
	?>
</form>
<script type="text/javascript">
	OSM.jQuery(function($){
		$(document).ready(function()
		{
			OSMVALIDATEFORM("osm_form_renew");
		})
	});
</script>
</div>