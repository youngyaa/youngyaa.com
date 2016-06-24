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

class EventbookingModelConfiguration extends RADModel
{
	/**
	 * Store the configuration data
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function store($data)
	{
		$db = $this->getDbo();
		$db->truncateTable('#__eb_configs');
		$row = $this->getTable('Config');
		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				$value = implode(',', $value);
			}
			$row->id           = 0;
			$row->config_key   = $key;
			$row->config_value = $value;
			$row->store();
		}

		$query = $db->getQuery(true);
		if ($data['activate_invoice_feature'])
		{
			$query->update('#__extensions')
				->set('`enabled`= 1')
				->where('`element`="invoice"')
				->where('`folder`="eventbooking"');
			$db->setQuery($query);
			$db->execute();
		}
		if ($data['multiple_booking'])
		{
			$query->clear();
			$query->update('#__extensions')
				->set('`enabled`= 1')
				->where('`element`="cartupdate"')
				->where('`folder`="eventbooking"');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}