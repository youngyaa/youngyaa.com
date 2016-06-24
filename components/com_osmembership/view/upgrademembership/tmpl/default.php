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
OSMembershipHelperJquery::validateForm();
?>
<div id="osm-profile-page" class="osm-container">
<h1 class="osm-page-title"><?php echo JText::_('OSM_UPGRADE_MEMBERSHIP'); ?></h1>
<?php
	if (count($this->upgradeRules))
	{
	?>
		<p class="osm-description"><?php echo JText::_('OSM_UPGRADE_MEMBERSHIP_DESCRIPTION'); ?></p>
		<form action="<?php echo JRoute::_('index.php?option=com_osmembership&task=register.process_upgrade_membership&Itemid='.$this->Itemid, false, $this->config->use_https ? 1 : 0); ?>" method="post" name="osm_form_update_membership" id="osm_form_update_membership" autocomplete="off" class="form form-horizontal">
			<ul class="osm-upgrade-options">
				<?php
				$upgradeOptionCount = 0;
				$plans = $this->plans;
				foreach ($this->upgradeRules as $rule)
				{
					$upgradeOptionCount++;
					$upgradeToPlan = $plans[$rule->to_plan_id];
					$symbol = $upgradeToPlan->currency_symbol ? $upgradeToPlan->currency_symbol : $upgradeToPlan->currency;
				?>
					<li class="osm-upgrade-option">
						<input type="radio" class="validate[required]" id="upgrade_option_id_<?php echo $upgradeOptionCount; ?>" name="upgrade_option_id" value="<?php echo $rule->id; ?>" />
						<label for="upgrade_option_id_<?php echo $upgradeOptionCount; ?>"><?php JText::printf('OSM_UPGRADE_OPTION_TEXT', $plans[$rule->from_plan_id]->title, $upgradeToPlan->title, OSMembershipHelper::formatCurrency($rule->price, $this->config, $symbol)); ?></label>
					</li>
				<?php
				}
				?>
			</ul>
			<div class="form-actions">
				<input type="submit" class="<?php echo $this->bootstrapHelper->getClassMapping('btn'); ?> btn-primary" value="<?php echo JText::_('OSM_PROCESS_UPGRADE'); ?>"/>
			</div>

		</form>
		<script type="text/javascript">
			OSM.jQuery(function($){
				$(document).ready(function(){
					OSMVALIDATEFORM("#osm_form_update_membership");
				})
			});	;
		</script>
	<?php
	}
	else
	{
	?>
		<p class="text-info"><?php echo JText::_('OSM_NO_UPGRADE_OPTIONS_AVAILABLE'); ?></p>
	<?php
	}
?>
</div>