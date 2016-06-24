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

class plgOSMembershipProcessrenewal extends JPlugin
{
	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	function onMembershipActive($row)
	{
		if ($row->user_id && $row->act == 'renew')
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Find the first subscription record of the user of this plan
			$query->select('*')
				->from('#__osmembership_subscribers')
				->where('user_id = '. $row->user_id)
				->where('plan_id = '. $row->plan_id)
				->where('(published = 1 OR published = 2)')
				->order('id');
			$db->setQuery($query, 0, 1);
			$rowSubscriber = $db->loadObject();
			if ($rowSubscriber)
			{
				$query->clear();
				$query->update('#__osmembership_subscribers')
					->set('published = 1')
					->set('to_date = '. $db->quote($row->to_date))
					->set('transaction_id = '. $db->quote($row->transaction_id))
					->set('subscription_code = '. $db->quote($row->subscription_code))
					->set('act = "renew"')
					->set('first_reminder_sent = 0')
					->set('second_reminder_sent = 0')
					->set('renewal_count = renewal_count + 1')
					->where('id = '. $rowSubscriber->id);
				$db->setQuery($query);
				$db->execute();

				// Delete all other subscription records to keep the management clean
				$query->clear();
				$query->delete('#__osmembership_subscribers')
					->where('user_id = '. $row->user_id)
					->where('plan_id = '. $row->plan_id)
					->where('id != '. $rowSubscriber->id);
				$db->execute();
			}
		}
	}
}	