<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class OSMembershipViewSubscriptioncancelHtml extends MPFViewHtml
{
	public $hasModel = false;

	public function display()
	{
		$this->setLayout('default');
		$db               = JFactory::getDbo();
		$query            = $db->getQuery(true);
		$input            = JFactory::getApplication()->input;
		$subscriptionCode = $input->get('subscription_code', '', 'none');
		$query->select('*')
			->from('#__osmembership_subscribers')
			->where('subscription_code = ' . $db->quote($subscriptionCode));
		$db->setQuery($query);
		$rowSubscriber = $db->loadObject();
		if (!$rowSubscriber)
		{
			JFactory::getApplication()->redirect('index.php', JText::_('Invalid subscription code'));
		}

		$messageObj  = OSMembershipHelper::getMessages();
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		if (strlen(strip_tags($messageObj->{'recurring_subscription_cancel_message' . $fieldSuffix})))
		{
			$message = $messageObj->{'recurring_subscription_cancel_message' . $fieldSuffix};
		}
		else
		{
			$message = $messageObj->recurring_subscription_cancel_message;
		}

		// Get plan title
		$query->clear();
		$query->select('a.*, a.title' . $fieldSuffix . ' AS title')
			->from('#__osmembership_plans AS a')
			->where('id = ' . $rowSubscriber->plan_id);
		$db->setQuery($query);
		$rowPlan = $db->loadObject();
		$message = str_replace('[PLAN_TITLE]', $rowPlan->title, $message);

		// Get latest subscription end date
		$query->clear();
		$query->select('MAX(to_date)')
			->from('#__osmembership_subscribers')
			->where('user_id = ' . $rowSubscriber->user_id)
			->where('plan_id = ' . $rowSubscriber->plan_id);
		$db->setQuery($query);
		$subscriptionEndDate = $db->loadResult();
		if (!$subscriptionEndDate)
		{
			$subscriptionEndDate = date(OSMembershipHelper::getConfigValue('date_format'));
		}
		$message       = str_replace('[SUBSCRIPTION_END_DATE]', $subscriptionEndDate, $message);
		$this->message = $message;

		parent::display();
	}
}