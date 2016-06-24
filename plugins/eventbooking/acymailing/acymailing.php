<?php
/**
 * @version            2.0.4
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2015 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

class plgEventBookingAcymailing extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFactory::getLanguage()->load('plg_eventbooking_acymailing', JPATH_ADMINISTRATOR);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
	}

	/**
	 * Render setting form
	 *
	 * @param JTable $row
	 *
	 * @return array
	 */
	public function onEditEvent($row)
	{
		if (!is_dir(JPATH_ADMINISTRATOR . '/components/com_acymailing'))
		{
			return array('title' => JText::_('PLG_EB_ACYMAILING_LIST_SETTINGS'),
			             'form'  => JText::_('Please install component Acymailing')
			);
		}
		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_EB_ACYMAILING_LIST_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param event   $row
	 * @param Boolean $isNew true if create new plan, false if edit
	 */
	public function onAfterSaveEvent($row, $data, $isNew)
	{
		// $row of table EB_plans
		$params = new JRegistry($row->params);
		$params->set('acymailing_list_ids', implode(',', $data['acymailing_list_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run when a membership activated
	 *
	 * @param PlanOsMembership $row
	 */
	public function onAfterStoreRegistrant($row)
	{
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_acymailing/acymailing.php'))
		{
			return;
		}
		$event =  JTable::getInstance('EventBooking', 'Event');
		$event->load($row->event_id);
		$params  = new JRegistry($event->params);
		$listIds = $params->get('acymailing_list_ids', '');
		if ($listIds != '')
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php';
			$userClass = acymailing_get('class.subscriber');
			$subId     = $userClass->subid($row->email);
			if (!$subId)
			{
				$myUser         = new stdClass();
				$myUser->email  = $row->email;
				$myUser->name   = $row->first_name . ' ' . $row->last_name;
				$myUser->userid = $row->user_id;
				$eventClass     = acymailing_get('class.subscriber');
				$subId          = $eventClass->save($myUser); //this
			}
			$listIds  = explode(',', $listIds);
			$newEvent = array();
			foreach ($listIds as $listId)
			{
				$newList           = array();
				$newList['status'] = 1;
				$newEvent[$listId] = $newList;
			}
			
			$userClass->saveSubscription($subId, $newEvent);
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
		$listClass = acymailing_get('class.list');
		$allLists  = $listClass->getLists();
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_EB_ACYMAILING_ASSIGN_TO_LIST_USER'); ?>
				</td>
				<td>
					<?php echo JHtml::_('select.genericlist', $allLists, 'acymailing_list_ids[]', 'class="inputbox" multiple="multiple" size="10"', 'listid', 'name', $listIds) ?>
				</td>
				<td>
					<?php echo JText::_('PLG_EB_ACYMAILING_ASSIGN_TO_LIST_USER_EXPLAIN'); ?>
				</td>
			</tr>
		</table>
	<?php
	}
}	