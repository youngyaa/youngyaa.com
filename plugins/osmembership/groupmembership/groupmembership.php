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

class plgOSMembershipGroupmembership extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
	}
	
	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	public function onMembershipActive($row)
	{
		if ($row->user_id && !$row->group_admin_id)
		{
			// Change subscription end date of the group members
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('MAX(to_date)')
				->from('#__osmembership_subscribers')
				->where('user_id=' . $row->user_id . ' AND plan_id=' . $row->plan_id . ' AND published = 1');
			$db->setQuery($query);
			$maxToDate = $db->loadResult();
			if ($maxToDate)
			{
				$query->clear();
				$query->update('#__osmembership_subscribers')
					->set('published = 1')
					->set('to_date = ' . $db->quote($maxToDate))
					->where('group_admin_id = ' . $row->user_id)
					->where('plan_id = ' . $row->plan_id);
				$db->setQuery($query);
				$db->execute();

				// Need to trigger onMembershipActive event
				$query->clear();
				$query->select('*')
					->from('#__osmembership_subscribers')
					->where('plan_id = ' . $row->plan_id)
					->where('group_admin_id = ' . $row->user_id);
				$db->setQuery($query);
				$groupMembers = $db->loadObjectList();
				if (count($groupMembers))
				{
					// Dispatcher
					$dispatcher = JDispatcher::getInstance();
					foreach ($groupMembers as $groupMember)
					{
						$dispatcher->trigger('onMembershipActive', array($groupMember));
					}

					// Update subscription status to active, just in case they were marked as expired before for some reasons
					$query->clear();
					$query->update('#__osmembership_subscribers')
						->set('published = 1')
						->where('plan_id = ' . $row->plan_id)
						->where('group_admin_id = ' . $row->user_id);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	/**
	 * Run when a membership expired die
	 *
	 * @param PlanOsMembership $row
	 */
	public function onMembershipExpire($row)
	{
		if ($row->user_id && !$row->group_admin_id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__osmembership_subscribers')
				->where('published = 1')
				->where('plan_id = ' . $row->plan_id)
				->where('user_id = ' . $row->user_id);
			$db->setQuery($query);
			$total = (int) $db->loadResult();
			if (!$total)
			{
				// Expired subscription, so need to trigger all group members as expired
				$query->clear();
				$query->select('*')
					->from('#__osmembership_subscribers')
					->where('plan_id = ' . $row->plan_id)
					->where('group_admin_id = ' . $row->user_id);
				$db->setQuery($query);
				$groupMembers = $db->loadObjectList();
				if (count($groupMembers))
				{
					// Dispatcher
					$dispatcher = JDispatcher::getInstance();
					foreach ($groupMembers as $groupMember)
					{
						$dispatcher->trigger('onMembershipExpire', array($groupMember));
					}

					// Need to mark the subscription as expired
					$query->clear();
					$query->update('#__osmembership_subscribers')
						->set('published = 2')
						->where('plan_id = ' . $row->plan_id)
						->where('group_admin_id = ' . $row->user_id);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
}	