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

class plgOSMembershipJoomlagroups extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
	}

	/**
	 * Render settings from
	 *
	 * @param PlanOSMembership $row
	 */
	function onEditSubscriptionPlan($row)
	{
		ob_start();
		$this->_drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_OSMEMBERSHIP_JOOMLA_GROUPS_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database
	 *
	 * @param PlanOsMembership $row
	 * @param Boolean          $isNew true if create new plan, false if edit
	 */
	function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		$params = new JRegistry($row->params);
		$params->set('joomla_group_ids', implode(',', $data['joomla_group_ids']));
		$params->set('subscription_expired_joomla_group_ids', implode(',', $data['subscription_expired_joomla_group_ids']));
		$params->set('joomla_expried_group_ids', implode(',', $data['joomla_expried_group_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	function onMembershipActive($row)
	{
		if ($row->user_id)
		{
			$user          = JFactory::getUser($row->user_id);
			$currentGroups = $user->get('groups');
			$plan          = JTable::getInstance('Osmembership', 'Plan');
			$plan->load($row->plan_id);
			$params        = new JRegistry($plan->params);
			$groups        = explode(',', $params->get('joomla_group_ids'));
			$subscriptionExpiredGroupIds = explode(',', $params->get('subscription_expired_joomla_group_ids'));
			$currentGroups = array_diff($currentGroups, $subscriptionExpiredGroupIds);
			$currentGroups = array_unique(array_merge($currentGroups, $groups));
			if ($row->group_admin_id > 0 && JPluginHelper::isEnabled('osmembership', 'groupmembership'))
			{
				// This is group member, need to exclude from some groups if needed
				$plugin = JPluginHelper::getPlugin('osmembership', 'groupmembership');
				if ($plugin)
				{
					$params          = new JRegistry($plugin->params);
					$excludeGroupIds = $params->get('exclude_group_ids', '');
					if ($excludeGroupIds)
					{
						$excludeGroupIds = explode(',', $excludeGroupIds);
						JArrayHelper::toInteger($excludeGroupIds);
						$currentGroups = array_diff($currentGroups, $excludeGroupIds);
					}
				}
			}
			$user->set('groups', $currentGroups);
			$user->save(true);
		}
	}

	/**
	 * Run when a membership expiried die
	 *
	 * @param PlanOsMembership $row
	 */
	function onMembershipExpire($row)
	{
		if ($row->user_id)
		{
			$user          = JFactory::getUser($row->user_id);
			$currentGroups = $user->get('groups');
			$plan          = JTable::getInstance('Osmembership', 'Plan');
			$plan->load($row->plan_id);
			$params                      = new JRegistry($plan->params);
			$groups                      = explode(',', $params->get('joomla_expried_group_ids'));
			$subscriptionExpiredGroupIds = explode(',', $params->get('subscription_expired_joomla_group_ids'));
			$activePlans                 = OSMembershipHelper::getActiveMembershipPlans($row->user_id, array($row->id));
			// Subscribers will be assigned to this group if he has no more active subscription of this plan, haven't renewed yet
			if (!in_array($row->plan_id, $activePlans))
			{
				$currentGroups = array_merge($currentGroups, $subscriptionExpiredGroupIds);
			}

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
				->from('#__osmembership_plans')
				->where('id IN  (' . implode(',', $activePlans) . ')');
			$db->setQuery($query);
			$rowPlans = $db->loadObjectList();
			if (count($rowPlans))
			{
				foreach ($rowPlans as $rowPlan)
				{
					$planParams = new JRegistry($rowPlan->params);
					$planGroups = explode(',', $planParams->get('joomla_group_ids'));
					$groups     = array_diff($groups, $planGroups);
				}
			}
			$currentGroups = array_unique(array_diff($currentGroups, $groups));
			$user->set('groups', $currentGroups);
			$user->save(true);
		}
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param object $row
	 *
	 */
	function _drawSettingForm($row)
	{
		$params                            = new JRegistry($row->params);
		$joomla_group_ids                  = explode(',', $params->get('joomla_group_ids', ''));
		$joomla_expried_group_ids          = explode(',', $params->get('joomla_expried_group_ids', ''));
		$subscriptionExpiredJoomlaGroupIds = explode(',', $params->get('subscription_expired_joomla_group_ids', ''));
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_ASSIGN_TO_JOOMLA_GROUPS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('access.usergroup', 'joomla_group_ids[]', $joomla_group_ids, ' multiple="multiple" size="6" ', false); ?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_ASSIGN_TO_JOOMLA_GROUPS_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_ASSIGN_TO_JOOMLA_GROUPS_EXPIRED'); ?>
				</td>
				<td>
					<?php echo JHtml::_('access.usergroup', 'subscription_expired_joomla_group_ids[]', $subscriptionExpiredJoomlaGroupIds, ' multiple="multiple" size="6" ', false); ?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_ASSIGN_TO_JOOMLA_GROUPS_EXPIRED_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_REMOVE_FROM_JOOMLA_GROUPS'); ?>
				</td>
				<td>
					<?php
					echo JHtml::_('access.usergroup', 'joomla_expried_group_ids[]', $joomla_expried_group_ids, ' multiple="multiple" size="6" ', false);
					?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_REMOVE_FROM_JOOMLA_GROUPS_EXPLAIN'); ?>
				</td>
			</tr>
		</table>
	<?php
	}
}	