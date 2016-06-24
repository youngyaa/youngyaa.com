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

error_reporting(0);

class plgSystemOSMembershipUpdateStatus extends JPlugin
{
	public function onAfterRender()
	{
		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
			$db    = JFactory::getDbo();
			$sql = ' SELECT a.* FROM #__osmembership_subscribers AS a '
				. ' INNER JOIN #__osmembership_plans AS b ON a.plan_id = b.id '
				. ' WHERE b.lifetime_membership !=1 AND a.published=1 AND a.to_date < NOW() ORDER BY a.to_date ';
			$db->setQuery($sql);
			$rows = $db->loadObjectList();
			if (count($rows))
			{
				$ids  = array();
				//Load Plugin to trigger OnMembershipExpire event
				JPluginHelper::importPlugin('osmembership');
				$dispatcher = JDispatcher::getInstance();
				foreach ($rows as $row)
				{
					//Trigger plugins
					$dispatcher->trigger('onMembershipExpire', array($row));
					$ids[] = $row->id;
				}
				$sql = 'UPDATE #__osmembership_subscribers SET published=2 WHERE id IN (' . implode(',', $ids) . ')';
				$db->setQuery($sql);
				$db->execute();
			}
		}

		return true;
	}
}
