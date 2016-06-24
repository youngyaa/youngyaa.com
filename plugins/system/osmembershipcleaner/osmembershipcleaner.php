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

/**
 * OS Membership Accounts cleaner Plugin
 *
 * @package        Joomla
 * @subpackage     OS Membership
 */
class plgSystemOSMembershipCleaner extends JPlugin
{
	public function onAfterRender()
	{
		$secretCode = trim($this->params->get('secret_code'));
		if ($secretCode && (JFactory::getApplication()->input->getString('secret_code') != $secretCode))
		{
			return;
		}

		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			$lastRun   = (int) $this->params->get('last_run', 0);
			$now       = time();
			$cacheTime = 3600 * 6; // The cleaner process will be run every 6 hours

			if (($now - $lastRun) < $cacheTime)
			{
				return;
			}

			//Store last run time
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$this->params->set('last_run', $now);
			$params = $this->params->toString();
			$query->clear();
			$query->update('#__extensions')
				->set('params=' . $db->quote($params))
				->where('`element`="osmembershipcleaner"')
				->where('`folder`="system"');

			try
			{
				// Lock the tables to prevent multiple plugin executions causing a race condition
				$db->lockTable('#__extensions');
			}
			catch (Exception $e)
			{
				// If we can't lock the tables it's too risk continuing execution
				return;
			}

			try
			{
				// Update the plugin parameters
				$result = $db->setQuery($query)->execute();
				$this->clearCacheGroups(array('com_plugins'), array(0, 1));
			}
			catch (Exception $exc)
			{
				// If we failed to execite
				$db->unlockTables();
				$result = false;
			}
			try
			{
				// Unlock the tables after writing
				$db->unlockTables();
			}
			catch (Exception $e)
			{
				// If we can't lock the tables assume we have somehow failed
				$result = false;
			}
			// Abort on failure
			if (!$result)
			{
				return;
			}

			$query->clear();
			$now = JFactory::getDate()->toSql();
			$query->select('id, user_id')
				->from('#__osmembership_subscribers')
				->where('published=0')
				->where('payment_method NOT LIKE "os_offline%"')
				->where("TIMESTAMPDIFF(HOUR, created_date, '$now') >= 5");
			$db->setQuery($query);
			$rowPendingSubscribers = $db->loadObjectList();
			if (count($rowPendingSubscribers))
			{
				$subscriberIds = array();
				foreach ($rowPendingSubscribers as $subscriber)
				{
					if ($subscriber->user_id > 0)
					{
						$user = JFactory::getUser($subscriber->user_id);
						if ($user->id && $user->get('block') && !$user->authorise('core.admin'))
						{
							$user->delete();
						}
					}
					$subscriberIds[] = $subscriber->id;
				}
				$query->clear();
				$query->delete('#__osmembership_subscribers')
					->where('id IN (' . implode(',', $subscriberIds) . ')');
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}

	/**
	 * Clears cache groups. We use it to clear the plugins cache after we update the last run timestamp.
	 *
	 * @param   array $clearGroups  The cache groups to clean
	 * @param   array $cacheClients The cache clients (site, admin) to clean
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	private function clearCacheGroups(array $clearGroups, array $cacheClients = array(0, 1))
	{
		$conf = JFactory::getConfig();
		foreach ($clearGroups as $group)
		{
			foreach ($cacheClients as $client_id)
			{
				try
				{
					$options = array(
						'defaultgroup' => $group,
						'cachebase'    => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' :
							$conf->get('cache_path', JPATH_SITE . '/cache')
					);
					$cache   = JCache::getInstance('callback', $options);
					$cache->clean();
				}
				catch (Exception $e)
				{
					// Ignore it
				}
			}
		}
	}
}
