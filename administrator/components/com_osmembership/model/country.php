<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * OSMembership Component Country Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelCountry extends MPFModelAdmin
{
	/**
	 * Update country_id make it the same with id
	 *
	 * @param JTable   $row
	 * @param MPFInput $input
	 * @param bool     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		if ($isNew)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->update('#__osmembership_countries')
				->set('country_id=id')
				->where('id=' . $row->id);
			$db->setQuery($query);
			$db->execute();
		}
	}
}