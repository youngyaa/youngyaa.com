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

class EventbookingModelLocation extends RADModelAdmin
{

	/**
	 * Pre-process location data before it is being saved to database
	 *
	 * @param JTable   $row
	 * @param RADInput $input
	 * @param bool     $isNew
	 */
	protected function beforeStore($row, $input, $isNew)
	{
		if ($isNew)
		{
			$row->user_id = JFactory::getUser()->id;
		}
		$coordinates = $input->get('coordinates', '', 'none');
		$coordinates = explode(',', $coordinates);
		$row->lat    = $coordinates[0];
		$row->long   = $coordinates[1];
	}
}