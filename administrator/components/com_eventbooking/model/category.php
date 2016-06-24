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

class EventbookingModelCategory extends RADModelAdmin
{
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param JTable $row A JTable object.
	 *
	 * @return array An array of conditions to add to ordering queries.
	 */

	protected function getReorderConditions($row)
	{
		return array('`parent` = ' . (int) $row->parent);
	}
}