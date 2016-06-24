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

class EventbookingModelRegistrant extends EventbookingModelCommonRegistrant
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
	 * Resend confirmation email to registrant
	 *
	 * @param $id
	 *
	 * @return bool True if email is successfully delivered
	 */
	public function resendEmail($id)
	{
		$row = $this->getTable();
		$row->load($id);
		if ($row->group_id > 0)
		{
			// We don't send email to group members, return false
			return false;
		}

		// Load the default frontend language
		$lang = JFactory::getLanguage();
		$tag  = $row->language;
		if (!$tag || $tag == '*')
		{
			$tag = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
		}
		$lang->load('com_eventbooking', JPATH_ROOT, $tag);

		$config = EventbookingHelper::getConfig();
		EventbookingHelper::sendEmails($row, $config);

		return true;
	}

	/**
	 * Method to remove registrants
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function delete($cid = array())
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$row   = $this->getTable();
		if (count($cid))
		{
			foreach ($cid as $registrantId)
			{
				$row->load($registrantId);
				if ($row->group_id > 0)
				{
					$query->update('#__eb_registrants')
						->set('number_registrants = number_registrants -1')
						->where('id=' . $row->group_id);
					$db->setQuery($query);
					$db->execute();
					$query->clear();

					$query->select('number_registrants')
						->from('#__eb_registrants')
						->where('id=' . $row->group_id);
					$db->setQuery($query);
					$numberRegistrants = (int) $db->loadResult();
					$query->clear();
					if ($numberRegistrants == 0)
					{
						$query->delete('#__eb_field_values')->where('registrant_id=' . $row->group_id);
						$db->setQuery($query);
						$db->execute();
						$query->clear();

						$sql = 'DELETE FROM #__eb_registrants WHERE id = ' . $row->group_id;
						$db->setQuery($sql);
						$db->execute();
						$query->clear();
					}
				}
			}
			$cids = implode(',', $cid);
			$query->select('id')
				->from('#__eb_registrants')
				->where('group_id IN (' . $cids . ')');
			$db->setQuery($query);
			$cid = array_merge($cid, $db->loadColumn());
			$query->clear();

			$registrantIds = implode(',', $cid);

			$query->delete('#__eb_field_values')->where('registrant_id IN (' . $registrantIds . ')');
			$db->setQuery($query);
			$db->execute();
			$query->clear();

			$query->delete('#__eb_registrants')->where('id IN (' . $registrantIds . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param array $cid   A list of the primary keys to change.
	 * @param int   $state The value of the published state.
	 *
	 * @throws Exception
	 */
	public function publish($cid, $state = 1)
	{
		$db = $this->getDbo();
		if (($state == 1) && count($cid))
		{
			JPluginHelper::importPlugin('eventbooking');
			$dispatcher = JDispatcher::getInstance();
			$config     = EventbookingHelper::getConfig();
			$row        = new RADTable('#__eb_registrants', 'id', $db);
			foreach ($cid as $registrantId)
			{
				$row->load($registrantId);
				if (!$row->published)
				{
					EventbookingHelper::sendRegistrationApprovedEmail($row, $config);

					// Trigger event
					$dispatcher->trigger('onAfterPaymentSuccess', array($row));
				}
			}
		}

		$cids  = implode(',', $cid);
		$query = $db->getQuery(true);
		$query->update('#__eb_registrants')
			->set('published = ' . (int) $state)
			->where("(id IN ($cids) OR group_id IN ($cids))")
			->where("payment_method LIKE 'os_offline%'");
		$db->setQuery($query);
		$db->execute();
	}
}