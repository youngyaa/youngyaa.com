<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();
class OSMembershipHelperDatabase
{
	/**
	 * Get category data from database
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getCategory($id)
	{
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		$query->select('*')
			->from('#__osmembership_categories')
			->where('id=' . (int) $id);

		if ($fieldSuffix)
		{
			self::getMultilingualFields($query, array('title', 'description'), $fieldSuffix);
		}

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Helper method to get fields from database table in case the site is multilingual
	 *
	 * @param JDatabaseQuery $query
	 * @param array          $fields
	 * @param                $fieldSuffix
	 */
	public static function getMultilingualFields(JDatabaseQuery $query, $fields = array(), $fieldSuffix)
	{
		foreach ($fields as $field)
		{
			$alias  = $field;
			$dotPos = strpos($field, '.');
			if ($dotPos !== false)
			{
				$alias = substr($field, $dotPos + 1);
			}
			$query->select($query->quoteName($field . $fieldSuffix, $alias));
		}
	}
}