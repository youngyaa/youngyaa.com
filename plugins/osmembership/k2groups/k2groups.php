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

class plgOSMembershipK2groups extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->canRun = file_exists(JPATH_ROOT . '/components/com_k2/k2.php');
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
	}

	/**
	 * Render settings from
	 *
	 * @param $row
	 *
	 * @return array
	 */
	public function onEditSubscriptionPlan($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_OSMEMBERSHIP_K2_GROUPS_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database
	 *
	 * @param $context
	 * @param $row
	 * @param $data
	 * @param $isNew
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->canRun)
		{
			return;
		}
		$params = new JRegistry($row->params);
		$params->set('k2_group_id', $data['k2_group_id']);
		$params->set('k2_expired_group_id', $data['k2_expired_group_id']);
		$row->params = $params->toString();
		$row->store();
	}

	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	public function onMembershipActive($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		
		if ($row->user_id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('`params`')
				->from('#__osmembership_plans')
				->where('id = ' . (int) $row->plan_id);
			$db->setQuery($query);
			$params    = new JRegistry($db->loadResult());
			$k2GroupId = (int) $params->get('k2_group_id', '');
			if ($k2GroupId)
			{
				$this->assignUserToK2Group($row->user_id, $k2GroupId);
			}
		}
	}

	/**
	 * Run when a membership expired
	 *
	 * @param PlanOsMembership $row
	 */
	public function onMembershipExpire($row)
	{
		if (!$this->canRun)
		{
			return;
		}
		if ($row->user_id)
		{
			$activePlans = OSMembershipHelper::getActiveMembershipPlans($row->user_id, array($row->id));
			if (!count($activePlans))
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('`params`')
					->from('#__osmembership_plans')
					->where('id = ' . (int) $row->plan_id);
				$db->setQuery($query);
				$params           = new JRegistry($db->loadResult());
				$k2GroupId        = (int) $params->get('k2_group_id', '');
				$k2ExpiredGroupId = (int) $params->get('k2_expired_group_id', '');
				if ($k2GroupId || $k2ExpiredGroupId)
				{
					$this->assignUserToK2Group($row->user_id, $k2GroupId);
				}
			}

		}
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param object $row
	 *
	 */
	private function drawSettingForm($row)
	{
		$params           = new JRegistry($row->params);
		$k2GroupId        = $params->get('k2_group_id', '');
		$k2ExpiredGroupId = $params->get('k2_expired_group_id', '');
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$query->select('id AS value, name AS text')->from('#__k2_user_groups');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('PLG_OSMEMBERSHIP_SELECT_K2_GROUP')));
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_K2GROUP_ASSIGN_TO_GROUP'); ?>
				</td>
				<td>
					<?php echo JHtml::_('select.genericlist', $options, 'k2_group_id', '', 'value', 'text', $k2GroupId);?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_K2GROUP_ASSIGN_TO_GROUP_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_K2GROUP_SUBSCRIPTION_EXPIRED_ASSIGN_TO_GROUPS'); ?>
				</td>
				<td>
					<?php echo JHtml::_('select.genericlist', $options, 'k2_expired_group_id', '', 'value', 'text', $k2ExpiredGroupId);?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_K2GROUP_SUBSCRIPTION_EXPIRED_ASSIGN_TO_GROUPS_EXPLAIN'); ?>
				</td>
			</tr>
		</table>
	<?php
	}


	/**
	 * Assign a user to selected K2 Group
	 *
	 * @param $userId
	 * @param $k2GroupId
	 */
	private function assignUserToK2Group($userId, $k2GroupId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		if ($k2GroupId)
		{
			$query->select('id')
				->from('#__k2_users')
				->where('userID =' . $userId);
			$db->setQuery($query);
			$k2UserId = $db->loadResult();
			if ($k2UserId)
			{
				$query->clear()->update('#__k2_users')->set('`group`=' . $k2GroupId)->where('id =' . $k2UserId);
			}
			else
			{
				$query->clear()->insert('#__k2_users')->set('`group`=' . $k2GroupId)->set('`userID`=' . $userId);
			}
			$db->setQuery($query);
			$db->execute();
		}
		else
		{
			// Remove him from K2 group settings
			$query->delete('#__k2_users')
				->where('`userID` = ' . $userId);
			$db->setQuery($query);
			$db->execute();
		}
	}
}