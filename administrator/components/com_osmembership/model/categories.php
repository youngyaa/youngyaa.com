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

class OSMembershipModelCategories extends MPFModelList
{
	/**
	 * Method to get categories data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$db     = $this->getDbo();
			$query  = $db->getQuery(true);
			$this->buildQueryColumns($query)
				->buildQueryFrom($query)
				->buildQueryJoins($query)
				->buildQueryWhere($query)
				->buildQueryGroup($query)
				->buildQueryOrder($query);
			$db->setQuery($query);
			$rows     = $db->loadObjectList();

			$children = array();

			// first pass - collect children
			if (count($rows))
			{
				foreach ($rows as $v)
				{
					$pt   = $v->parent_id;
					$list = @$children[$pt] ? $children[$pt] : array();
					array_push($list, $v);
					$children[$pt] = $list;
				}
			}
			$list  = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999);
			$total = count($list);
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($total, $this->state->limitstart, $this->state->limit);
			// slice out elements based on limits
			$list       = array_slice($list, $this->pagination->limitstart, $this->pagination->limit);
			$this->data = $list;
		}

		return $this->data;
	}
}