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

class EventBookingModelState extends RADModelAdmin
{
	/**
	 * Update state_id make it the same with id
	 *
	 * @param JTable   $row
	 * @param RADInput $input
	 * @param bool     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		if ($isNew)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->update('#__eb_states')
				->set('state_id = id')
				->where('id = ' . $input->getInt('id', 0));
			$db->setQuery($query);
			$db->execute();
		}
	}
}