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

if (empty($rowSubscriptions))
{
?>
	<p class="text-info"><?php echo JText::_('OSM_NO_ACTIVE_SUBSCRIPTIONS'); ?></p>
<?php
}
else
{
?>
	<ul class="osm-active-plans-list">
		<?php
			$todayDate = JFactory::getDate();
			foreach($rowSubscriptions as $rowSubscription)
			{
				$expiredDate = JFactory::getDate($rowSubscription->subscription_to_date);
				$numberDays = $todayDate->diff($expiredDate)->days;
				$membershipStatus = JText::_('OSM_MEMBERSHIP_STATUS');
				$membershipStatus = str_replace('[PLAN_TITLE]', $rowSubscription->title, $membershipStatus);
				$membershipStatus = str_replace('[EXPIRED_DATE]', JHtml::_('date', $rowSubscription->subscription_to_date, OSMembershipHelper::getConfigValue('date_format')), $membershipStatus);
				$membershipStatus = str_replace('[NUMBER_DAYS]', $numberDays, $membershipStatus);
			?>
				<li><?php echo $membershipStatus; ?></li>
			<?php
			}
		?>
	</ul>
<?php
}