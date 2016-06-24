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
 * OS Membership Reminder Plugin
 *
 * @package        Joomla
 * @subpackage     OS Membership
 */
class plgSystemOSMembershipReminder extends JPlugin
{

	/**
	 * The sending reminder emails is triggered after the page has fully rendered.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function onAfterRender()
	{
		$secretCode = trim($this->params->get('secret_code'));
		if ($secretCode && (JFactory::getApplication()->input->getString('secret_code') != $secretCode))
		{
			return;
		}
		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			$bccEmail                = $this->params->get('bcc_email', '');
			$numberEmailSendEachTime = (int) $this->params->get('number_subscribers', 5);
			$lastRun                 = (int) $this->params->get('last_run', 0);
			$now                     = time();
			$cacheTime               = 7200; // The reminder process will be run every 2 hours

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
				->where('`element`="osmembershipreminder"')
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

			require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';

			try
			{
				$query->clear();
				$query->select('a.id, a.plan_id, a.user_id, a.first_name, a.last_name, a.email, a.to_date, DATEDIFF(to_date, NOW()) AS number_days, b.title AS plan_title')
					->from('#__osmembership_subscribers AS a')
					->innerJoin('#__osmembership_plans AS b  ON a.plan_id = b.id')
					->where('a.published = 1 AND a.first_reminder_sent = 0  AND b.lifetime_membership != 1 AND  (b.send_first_reminder > 0 AND b.send_first_reminder >= DATEDIFF(to_date, NOW()))')
					->order('a.to_date');
				$db->setQuery($query, 0, $numberEmailSendEachTime);
				$rows = $db->loadObjectList();
				OSMembershipHelper::sendFirstReminderEmails($rows, $bccEmail);

				$query->clear();
				$query->select('a.id, a.plan_id, a.user_id, a.first_name, a.last_name, a.email, a.to_date, DATEDIFF(to_date, NOW()) AS number_days, b.title AS plan_title')
					->from('#__osmembership_subscribers AS a')
					->innerJoin('#__osmembership_plans AS b ON a.plan_id = b.id')
					->where('a.published = 1 AND a.second_reminder_sent = 0 AND b.lifetime_membership != 1 AND (b.send_second_reminder > 0 AND b.send_second_reminder >= DATEDIFF(to_date, NOW()))')
					->order('a.to_date');
				$db->setQuery($query, 0, $numberEmailSendEachTime);
				$rows = $db->loadObjectList();
				OSMembershipHelper::sendSecondReminderEmails($rows, $bccEmail);

				$query->clear();
				$query->select('a.id, a.plan_id, a.user_id, a.first_name, a.last_name, a.email, a.to_date, DATEDIFF(to_date, NOW()) AS number_days, b.title AS plan_title')
					->from('#__osmembership_subscribers AS a')
					->innerJoin('#__osmembership_plans AS b ON a.plan_id = b.id')
					->where('a.published = 1 AND a.third_reminder_sent = 0 AND b.lifetime_membership != 1 AND (b.send_third_reminder > 0 AND b.send_third_reminder >= DATEDIFF(to_date, NOW()))')
					->order('a.to_date');
				$db->setQuery($query, 0, $numberEmailSendEachTime);
				$rows = $db->loadObjectList();
				OSMembershipHelper::sendThirdReminderEmails($rows, $bccEmail);
			}
			catch (Exception $e)
			{
				// Ignore
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
