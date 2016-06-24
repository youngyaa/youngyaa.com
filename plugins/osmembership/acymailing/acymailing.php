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

class plgOSMembershipAcymailing extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');

		$this->canRun = file_exists(JPATH_ROOT . '/components/com_acymailing/acymailing.php');
	}

	/**
	 * Render setting form
	 *
	 * @param PlanOSMembership $row
	 *
	 * @return array
	 */
	public function onEditSubscriptionPlan($row)
	{
		if (!$this->canRun)
		{
			return array('title' => JText::_('PLG_OSMEMBERSHIP_ACYMAILING_LIST_SETTINGS'),
			             'form'  => JText::_('Please install component Acymailing')
			);
		}
		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_OSMEMBERSHIP_ACYMAILING_LIST_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param PlanOsMembership $row
	 * @param bool             $isNew true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->canRun)
		{
			return;
		}

		$params = new JRegistry($row->params);

		$params->set('acymailing_list_ids', implode(',', $data['acymailing_list_ids']));
		$params->set('subscription_expired_acymailing_list_ids', implode(',', $data['subscription_expired_acymailing_list_ids']));
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

		$plan = JTable::getInstance('Osmembership', 'Plan');
		$plan->load($row->plan_id);
		$params  = new JRegistry($plan->params);
		$listIds = $params->get('acymailing_list_ids', '');

		if ($listIds != '')
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php';
			$userClass = acymailing_get('class.subscriber');
			$subId     = $userClass->subid($row->email);
			if (!$subId)
			{
				$myUser          = new stdClass();
				$myUser->email   = $row->email;
				$myUser->name    = $row->first_name . ' ' . $row->last_name;
				$myUser->userid  = $row->user_id;
				$subscriberClass = acymailing_get('class.subscriber');
				$subId           = $subscriberClass->save($myUser); //this
			}

			$listIds = explode(',', $listIds);
			$db      = JFactory::getDbo();
			$query   = $db->getQuery(true);

			$time = time();
			foreach ($listIds as $listId)
			{
				$query->clear();
				$query->select('COUNT(*)')
					->from('#__acymailing_listsub')
					->where('listid = ' . (int) $listId)
					->where('subid = ' . $subId);
				$db->setQuery($query);
				$total = $db->loadResult();
				if (!$total)
				{
					$query->clear();
					$query->insert('#__acymailing_listsub')
						->columns('listid, subid, subdate, unsubdate, `status`')
						->values("$listId, $subId, $time, NULL, 1");
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	/**
	 * Run when a membership expiried die
	 *
	 * @param PlanOsMembership $row
	 */
	public function onMembershipExpire($row)
	{
		$plan = JTable::getInstance('Osmembership', 'Plan');
		$plan->load($row->plan_id);
		$params  = new JRegistry($plan->params);
		$listIds = trim($params->get('subscription_expired_acymailing_list_ids', ''));
		if ($listIds != '')
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php';

			$userClass = acymailing_get('class.subscriber');
			$subId     = $userClass->subid($row->email);
			if ($subId)
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->delete('#__acymailing_listsub')
					->where('subid = ' . $subId)
					->where('listid IN (' . $listIds . ')');
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param object $row
	 */
	private function drawSettingForm($row)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php';
		$params    = new JRegistry($row->params);
		$listIds   = explode(',', $params->get('acymailing_list_ids', ''));
		$expiredListIds   = explode(',', $params->get('subscription_expired_acymailing_list_ids', ''));
		$listClass = acymailing_get('class.list');
		$allLists  = $listClass->getLists();
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_ACYMAILING_ASSIGN_TO_LIST_USER'); ?>
				</td>
				<td>
					<?php echo JHtml::_('select.genericlist', $allLists, 'acymailing_list_ids[]', 'class="inputbox" multiple="multiple" size="10"', 'listid', 'name', $listIds) ?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_ACYMAILING_ASSIGN_TO_LIST_USER_EXPLAIN'); ?>
				</td>
			</tr>

			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_OSMEMBERSHIP_ACYMAILING_REMOVE_FROM_ACYMAILING_LISTS'); ?>
				</td>
				<td>
					<?php
						echo JHtml::_('select.genericlist', $allLists, 'subscription_expired_acymailing_list_ids[]', 'class="inputbox" multiple="multiple" size="10"', 'listid', 'name', $expiredListIds);
					?>
				</td>
				<td>
					<?php echo JText::_('PLG_OSMEMBERSHIP_ACYMAILING_REMOVE_FROM_ACYMAILING_LISTS_EXPLAIN'); ?>
				</td>
			</tr>

		</table>
	<?php
	}
}