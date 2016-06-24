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

class plgOSMembershipMembershipId extends JPlugin
{

	public function onAfterStoreSubscription($row)
	{
		if ($row->payment_method == 'os_offline' && !$row->membership_id)
		{
			$this->generateMembershipId($row);
		}
	}

	public function onMembershipActive($row)
	{
		if (!$row->membership_id)
		{
			$this->generateMembershipId($row);
		}

		return true;
	}

	/**
	 * Generate Membership ID for a subscription record
	 *
	 * @param $row
	 */
	private function generateMembershipId($row)
	{

		if ($row->user_id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('MAX(membership_id)')
				->from('#__osmembership_subscribers')
				->where('user_id = ' . $row->user_id);
			$db->setQuery($query);
			$membershipId = (int) $db->loadResult();;
			if ($membershipId)
			{
				$row->membership_id = $membershipId;
			}

		}

		if (!$row->membership_id)
		{
			$row->membership_id = OSMembershipHelper::getMembershipId();
		}

		$row->store();
	}
}
