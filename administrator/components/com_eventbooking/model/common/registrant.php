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
defined('_JEXEC') or die();

/**
 * Event Booking Registrant Model
 *
 * @package        Joomla
 * @subpackage     Event Booking
 */
class EventbookingModelCommonRegistrant extends RADModelAdmin
{

	/**
	 * Instantiate the model.
	 *
	 * @param array $config configuration data for the model
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->state->insert('filter_event_id', 'int', 0);
	}

	/**
	 * Initial registrant data
	 *
	 * @see RADModelAdmin::initData()
	 */
	public function initData()
	{
		parent::initData();
		$this->data->event_id = $this->state->filter_event_id;
	}

	/**
	 * Method to store a registrant
	 *
	 * @access    public
	 *
	 * @param    RADInput $input
	 *
	 * @return    boolean    True on success
	 */
	function store($input, $ignore = array())
	{
		$config = EventbookingHelper::getConfig();
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$row    = $this->getTable();
		$data   = $input->getData();
		if ($data['id'])
		{
			//We will need to calculate total amount here now
			$row->load($data['id']);
			$published = $row->published;
			if ($row->is_group_billing)
			{
				$rowFields = EventbookingHelper::getFormFields($data['event_id'], 1);
			}
			else
			{
				$rowFields = EventbookingHelper::getFormFields($data['event_id'], 0);
			}
			$row->bind($data);
			$row->store();
			$form = new RADForm($rowFields);
			$form->storeData($row->id, $data);
			//Update status of the record			
			if ($row->is_group_billing && (strpos($row->payment_method, 'os_offline') !== false))
			{
				$query->update('#__eb_registrants')
					->set('published=' . (int) $row->published)
					->where('group_id=' . $row->id);
				$db->setQuery($query);
				$db->execute();
				$query->clear();
			}
			//Store group members data
			if ($row->number_registrants > 1 && $config->collect_member_information)
			{
				$ids              = (array) $data['ids'];
				$memberFormFields = EventbookingHelper::getFormFields($row->event_id, 2);
				for ($i = 0, $n = count($ids); $i < $n; $i++)
				{
					$memberId  = $ids[$i];
					$rowMember = $this->getTable();
					$rowMember->load($memberId);
					$rowMember->published      = $row->published;
					$rowMember->payment_method = $row->payment_method;
					$rowMember->transaction_id = $row->transaction_id;
					$memberForm                = new RADForm($memberFormFields);
					$memberForm->setFieldSuffix($i + 1);
					$memberForm->bind($data);
					$memberForm->removeFieldSuffix();
					$memberData = $memberForm->getFormData();
					$rowMember->bind($memberData);
					$rowMember->store();
					$memberForm->storeData($rowMember->id, $memberData);
				}
			}
			if ($row->published == 1 && $published == 0)
			{
				//Change from pending to paid, trigger event, send emails
				JPluginHelper::importPlugin('eventbooking');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onAfterPaymentSuccess', array($row));
				EventbookingHelper::sendRegistrationApprovedEmail($row, $config);
			}
			elseif ($row->published == 2 && $published != 2 && $config->activate_waitinglist_feature)
			{
				//Registration is cancelled, send notification emails to waiting list
				EventbookingHelper::notifyWaitingList($row, $config);
			}
			$input->set('id', $row->id);

			return true;
		}
		else
		{
			$row->bind($data);
			$rowFields = EventbookingHelper::getFormFields($data['event_id'], 0);
			$form      = new RADForm($rowFields);
			$form->bind($data);
			$row->payment_method = 'os_offline';
			$row->register_date  = gmdate('Y-m-d');
			$rate                = EventbookingHelper::getRegistrationRate($data['event_id'], $data['number_registrants']);
			$row->total_amount   = $row->amount = $rate * $data['number_registrants'] + $form->calculateFee();
			if ($row->number_registrants > 1)
			{
				$row->is_group_billing = 1;
			}
			else
			{
				$row->is_group_billing = 0;
			}
			$row->store();
			$form->storeData($row->id, $data);

			if ($row->published == 1)
			{
				// Trigger event and send emails
				JPluginHelper::importPlugin('eventbooking');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onAfterPaymentSuccess', array($row));
			}
			$input->set('id', $row->id);

			return true;
		}

		return true;
	}
}