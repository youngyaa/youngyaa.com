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

class plgContentArticleRestriction extends JPlugin
{

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		if ($context != 'com_content.article')
		{
			return;
		}
		if (file_exists(JPATH_ROOT . '/components/com_osmembership/osmembership.php'))
		{
			if (is_object($row))
			{

				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('DISTINCT plan_id')
					->from('#__osmembership_articles')
					->where('article_id = ' . $row->id);
				$db->setQuery($query);
				try
				{
					$planIds = $db->loadColumn();
				}
				catch (Exception $e)
				{
					$planIds = array();
				}

				if (count($planIds))
				{
					//Check to see the current user has an active subscription plans
					require_once JPATH_ROOT . '/components/com_osmembership/helper/helper.php';
					$activePlans = OSMembershipHelper::getActiveMembershipPlans();
					if (!count(array_intersect($planIds, $activePlans)))
					{
						$message     = OSMembershipHelper::getMessages();
						$fieldSuffix = OSMembershipHelper::getFieldSuffix();
						if (strlen($message->{'content_restricted_message' . $fieldSuffix}))
						{
							$msg = $message->{'content_restricted_message' . $fieldSuffix};
						}
						else
						{
							$msg = $message->content_restricted_message;
						}

						//Get title of these subscription plans
						$query->clear();
						$query->select('title')
							->from('#__osmembership_plans')
							->where('published = 1')
							->where('id IN (' . implode(',', $planIds) . ')')
							->order('ordering');
						$db->setQuery($query);
						$planTitles = $db->loadColumn();
						$planTitles = implode(' OR ', $planTitles);
						$msg        = str_replace('[PLAN_TITLES]', $planTitles, $msg);

						$t[]       = $row->introtext;
						$t[]       = '<div class="text-info">' . $msg . '</div>';
						$row->text = implode(' ', $t);
					}
				}
			}
		}

		return true;
	}
}