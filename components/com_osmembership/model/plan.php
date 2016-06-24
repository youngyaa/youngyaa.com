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

class OSMembershipModelPlan extends MPFModel
{

	/**
	 * Constructor
	 *
	 * @param array $config
	 *
	 * @throws Exception
	 */
	function __construct($config)
	{
		parent::__construct($config);
		$this->state->insert('id', 'int', 0);
	}

	/**
	 * Get plan information from database
	 *
	 * @return mixed
	 */
	public function getData()
	{
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		$db          = $this->getDbo();
		$query       = $db->getQuery(true);
		$query->select('tbl.*')
			->select('tbl.title' . $fieldSuffix . ' AS title')
			->select('tbl.short_description' . $fieldSuffix . ' AS short_description')
			->select('tbl.description' . $fieldSuffix . ' AS description')
			->from('#__osmembership_plans AS tbl')
			->where('tbl.id = '. $this->state->id)
			->where('published = 1')
			->where('access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
		$db->setQuery($query);

		return $db->loadObject();
	}
}