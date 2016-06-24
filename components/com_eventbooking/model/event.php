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

require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/model/common/event.php';

class EventbookingModelEvent extends EventbookingModelCommonEvent
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

		$this->state->insert('catid', 'int', 0);
	}


	/**
	 * Get all necessary data of an event
	 *
	 * @return mixed
	 */
	public function getEventData()
	{
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$currentDate = JHtml::_('date', 'Now', 'Y-m-d H:i:s');
		$query->select('a.*')
			->select("DATEDIFF(event_date, '$currentDate') AS number_event_dates")
			->select("DATEDIFF('$currentDate', a.late_fee_date) AS late_fee_date_diff")
			->select("TIMESTAMPDIFF(MINUTE, registration_start_date, '$currentDate') AS registration_start_minutes")
			->select("TIMESTAMPDIFF(MINUTE, cut_off_date, '$currentDate') AS cut_off_minutes")
			->select("DATEDIFF(early_bird_discount_date, '$currentDate') AS date_diff")
			->select('IFNULL(SUM(b.number_registrants), 0) AS total_registrants')
			->from('#__eb_events AS a')
			->leftJoin('#__eb_registrants AS b ON (a.id = b.event_id AND b.group_id=0 AND (b.published = 1 OR (b.payment_method LIKE "os_offline%" AND b.published NOT IN (2,3))))')
			->where('a.id = ' . $this->state->id)
			->where('a.published = 1')
			->group('a.id');
		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, array('title', 'short_description', 'description', 'meta_keywords', 'meta_description'), $fieldSuffix);
		}
		$db->setQuery($query);
		$row = $db->loadObject();

		// Get additional information about the event
		if ($row)
		{
			// Get the main category of this event
			$query->clear();
			$query->select('category_id')
				->from('#__eb_event_categories')
				->where('event_id = ' . $this->state->id)
				->where('main_category = 1');
			$db->setQuery($query);
			$row->category_id = (int) $db->loadResult();

			// Calculate discounted price
			$rows = array($row);
			EventbookingHelperData::calculateDiscount($rows);

			// Apply tax rate to price for displaying purpose
			$config = EventbookingHelper::getConfig();
			if ($config->show_price_including_tax)
			{

				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row                    = $rows[$i];
					$taxRate                = $row->tax_rate;
					$row->individual_price  = round($row->individual_price * (1 + $taxRate / 100), 2);
					$row->fixed_group_price = round($row->fixed_group_price * (1 + $taxRate / 100), 2);
					if ($config->show_discounted_price)
					{
						$row->discounted_price = round($row->discounted_price * (1 + $taxRate / 100), 2);
					}
				}
			}

			return $rows[0];
		}

		return null;
	}
} 
