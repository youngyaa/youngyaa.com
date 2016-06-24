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

class plgEventbookingJoomlagroups extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFactory::getLanguage()->load('plg_eventbooking_joomlagroups', JPATH_ADMINISTRATOR);
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_eventbooking/table');
	}
	
	/**
	 * Render settings form
	 *
	 * @param $row
	 *
	 * @return array
	 */
	public function onEditEvent($row)
	{
		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return array('title' => JText::_('PLG_EVENTBOOKING_JOOMLA_GROUPS_SETTINGS'),
		             'form'  => $form
		);
	}

	/**
	 * Store setting into database
	 *
	 * @param Event   $row
	 * @param Boolean $isNew true if create new plan, false if edit
	 */
	public function onAfterSaveEvent($row, $data, $isNew)
	{
		$params = new JRegistry($row->params);
		$params->set('joomla_group_ids', implode(',', $data['joomla_group_ids']));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Add registrants to selected Joomla groups when payment for registration completed
	 *
	 * @param PlanOsMembership $row
	 */
	public function onAfterPaymentSuccess($row)
	{
		if ($row->user_id)
		{
			$user          = JFactory::getUser($row->user_id);
			$currentGroups = $user->get('groups');
			$event         = JTable::getInstance('EventBooking', 'Event');
			$eventIds      = array($row->event_id);
			$config        = EventbookingHelper::getConfig();
			if ($config->multiple_booking)
			{
				// Get all events which users register for in this cart registration
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('event_id')
					->from('#__eb_registrants')
					->where('cart_id=' . $row->id);
				$db->setQuery($query);
				$eventIds = array_unique(array_merge($eventIds, $db->loadColumn()));
			}
			// Calculate the groups which registrant should be assigned to
			foreach ($eventIds as $eventId)
			{
				$event->load($eventId);
				$params   = new JRegistry($event->params);
				$groupIds = $params->get('joomla_group_ids');
				if ($groupIds)
				{
					$groups        = explode(',', $groupIds);
					$currentGroups = array_unique(array_merge($currentGroups, $groups));
				}
			}
			$user->set('groups', $currentGroups);
			$user->save(true);
		}
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param object $row
	 *
	 */
	private function drawSettingForm($row)
	{
		$params           = new JRegistry($row->params);
		$joomla_group_ids = explode(',', $params->get('joomla_group_ids', ''));
		?>
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo JText::_('PLG_EVENTBOOKING_JOOMLA_ASSIGN_TO_JOOMLA_GROUPS'); ?>
				</td>
				<td>
					<?php
					echo JHtml::_('access.usergroup', 'joomla_group_ids[]', $joomla_group_ids, ' multiple="multiple" size="6" ', false);
					?>
				</td>
				<td>
					<?php echo JText::_('PLG_EVENTBOOKING_JOOMLA_ASSIGN_TO_JOOMLA_GROUPS_EXPLAIN'); ?>
				</td>
			</tr>
		</table>
	<?php
	}
}	