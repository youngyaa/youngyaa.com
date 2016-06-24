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

class EventbookingModelMessage extends RADModel
{
	/**
	 * Store the message data
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function store($data)
	{
		$db  = $this->getDbo();
		$row = new RADTable('#__eb_messages', 'id', $this->db);
		$db->truncateTable('#__eb_messages');
		foreach ($data as $key => $value)
		{
			$row->id          = 0;
			$row->message_key = $key;
			$row->message     = $value;
			$row->store();
		}

		return true;
	}
}